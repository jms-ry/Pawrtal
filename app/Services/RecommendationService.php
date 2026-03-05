<?php

namespace App\Services;

use App\Models\Rescue;
use App\Helpers\TextAnalysisHelper;
use App\Models\AdoptionApplication;

class RecommendationService
{
  protected $textAnalyzer;

  public function __construct()
  {
    $this->textAnalyzer = new TextAnalysisHelper();
  }

  /**
    * Get recommended rescues based on user preferences and household
  */
  public function getRecommendations(array $preferences, array $household, $userId = null)
  {
    // Get all available rescues
    $availableRescues = Rescue::where('adoption_status', 'available')->get();
    
    $excludeRescueIds = [];
    if ($userId) {
      $excludeRescueIds = AdoptionApplication::where('user_id', $userId)
        ->whereIn('status', ['pending', 'under_review', 'approved'])
        ->pluck('rescue_id')
      ->toArray();
    }
    
    $scoredRescues = [];

    foreach ($availableRescues as $rescue) {
      if (in_array($rescue->id, $excludeRescueIds)) {
        continue;
      }
        
      $score = $this->calculateCompatibilityScore($rescue, $preferences, $household);
        
      // Only include rescues with score >= 60
      if ($score >= 60) {
        $scoredRescues[] = [
          'rescue' => $rescue,
          'score' => $score,
          'match_percentage' => round($score),
          'reasons' => $this->getMatchReasons($rescue, $preferences, $household),
        ];
      }
    }

    // Sort by score (highest first)
    usort($scoredRescues, fn($a, $b) => $b['score'] <=> $a['score']);

    return $scoredRescues;
  }

  /**
    * Calculate compatibility score (0-100)
  */
  private function calculateCompatibilityScore($rescue, $preferences, $household)
  {
    $score = 0;

    // Extract traits from description using NLP
    $traits = $this->textAnalyzer->extractTraits($rescue->description);

    // 1. Size Match (20 points max: 20 direct, 17 any, 15 compatible, 10 partial)
    $score += $this->scoreSizeMatch($rescue->size, $preferences['size'], $household['house_structure']);

    // 2. Age Match (15 points max: 15 direct, 13 any, 7 partial)
    $score += $this->scoreAgeMatch($rescue->age, $preferences['age_preference']);

    // 3. Sex Match (5 points max: 5 direct, 3 any)
    $score += $this->scoreSexMatch($rescue->sex, $preferences['sex']);

    // 4. Energy Level Match (20 points max: 20 direct, 17 any, 10 partial)
    $score += $this->scoreEnergyMatch($traits, $preferences['energy_level']);

    // 5. Maintenance Level Match (15 points max: 15 direct, 13 any, 7 neutral)
    $score += $this->scoreMaintenanceMatch($traits, $preferences['maintenance_level']);

    // 6. Child Compatibility (10 points max: 10 good/no children, 8 partial, -10 penalty)
    $score += $this->scoreChildCompatibility($traits, $household['have_children']);

    // 7. Pet Compatibility (10 points max: 10 good/no pets, variable based on traits)
    $score += $this->scorePetCompatibility($traits, $household['has_other_pets'], $household['current_pets']);

    // 8. Temperament Match (5 points max: 5 direct match, 0 if any)
    if (isset($preferences['temperament']) && $preferences['temperament'] !== 'any') {
        $score += $this->scoreTemperamentMatch($traits, $preferences['temperament']);
    }

    // Total possible: 100 points (without species scoring)
    return max(0, min(100, $score));
  }

  private function scoreSizeMatch($rescueSize, $preferredSize, $houseStructure)
  {
    // Direct match - HIGHEST SCORE
    if ($preferredSize === $rescueSize) return 20;
    
    // No preference - GOOD SCORE (but less than direct match)
    if ($preferredSize === 'any') return 17;

    // Compatibility with living space
    $smallSpaces = ['apartment', 'condo', 'studio'];
    if (in_array(strtolower($houseStructure ?? ''), $smallSpaces) && $rescueSize === 'small') {
        return 15;
    }

    if ($rescueSize === 'large' && !in_array(strtolower($houseStructure ?? ''), $smallSpaces)) {
        return 15;
    }

    // Partial match (one size difference)
    $sizeOrder = ['small', 'medium', 'large'];
    $rescueIndex = array_search($rescueSize, $sizeOrder);
    $preferredIndex = array_search($preferredSize, $sizeOrder);
    
    if ($rescueIndex !== false && $preferredIndex !== false) {
      $difference = abs($rescueIndex - $preferredIndex);
      if ($difference === 1) return 10;
    }

    return 0;
  }

  private function scoreAgeMatch($rescueAge, $agePreference)
  {
    $ageInMonths = $this->textAnalyzer->parseAge($rescueAge);
    
    if ($ageInMonths === null) {
      // Unknown age - give neutral score
      return $agePreference === 'any' ? 13 : 8;
    }

    $ageCategory = $this->categorizeAge($ageInMonths);

    // Direct match - HIGHEST SCORE
    if ($agePreference === $ageCategory) return 15;
    
    // No preference - GOOD SCORE (but less than direct match)
    if ($agePreference === 'any') return 13;
    
    // Partial match (adjacent categories)
    $categories = ['puppy', 'young', 'adult', 'senior'];
    $rescueCatIndex = array_search($ageCategory, $categories);
    $prefIndex = array_search($agePreference, $categories);
    
    if ($rescueCatIndex !== false && $prefIndex !== false) {
      $difference = abs($rescueCatIndex - $prefIndex);
      if ($difference === 1) return 7;
    }

    return 0;
  }

  private function categorizeAge($ageInMonths)
  {
    if ($ageInMonths <= 6) return 'puppy';
    if ($ageInMonths <= 24) return 'young';
    if ($ageInMonths <= 84) return 'adult'; // Up to 7 years
    return 'senior';
  }

  private function scoreSexMatch($rescueSex, $preferredSex)
  {
    // Direct match - HIGHEST SCORE
    if ($preferredSex === strtolower($rescueSex)) return 5;
    
    // No preference - GOOD SCORE (but less than direct match)
    if ($preferredSex === 'any' || $preferredSex === 'no_preference') return 3;
    
    return 0;
  }

  private function scoreEnergyMatch($traits, $preferredEnergy)
  {
    // Direct matches - HIGHEST SCORE
    if ($preferredEnergy === 'high' && $traits['energetic']) return 20;
    if ($preferredEnergy === 'low' && $traits['calm']) return 20;
    if ($preferredEnergy === 'moderate' && !$traits['energetic'] && !$traits['calm']) return 20;
    
    // No preference - GOOD SCORE (but less than direct match)
    if ($preferredEnergy === 'any') return 17;

    // Partial matches
    if ($preferredEnergy === 'moderate' && ($traits['energetic'] || $traits['calm'])) return 10;

    return 0;
  }

  private function scoreMaintenanceMatch($traits, $preferredMaintenance)
  {
    // Infer maintenance from traits
    $isHighMaintenance = $traits['energetic'] || ($traits['needs_attention'] ?? false);
    $isLowMaintenance = $traits['calm'] || ($traits['independent'] ?? false);

    // Direct matches - HIGHEST SCORE
    if ($preferredMaintenance === 'high' && $isHighMaintenance) return 15;
    if ($preferredMaintenance === 'low' && $isLowMaintenance) return 15;
    if ($preferredMaintenance === 'moderate' && !$isHighMaintenance && !$isLowMaintenance) return 15;
    
    // No preference - GOOD SCORE (but less than direct match)
    if ($preferredMaintenance === 'any') return 13;

    return 7; // Neutral if can't determine
  }

  private function scoreChildCompatibility($traits, $hasChildren)
  {
    if (!$hasChildren) {
      // No children - neutral/good score
      return 10;
    }

    // Has children - prioritize child-friendly dogs
    if ($traits['good_with_children']) return 10;
    if ($traits['patient'] || $traits['gentle']) return 8;
    
    // Penalty if explicitly not good with children
    if ($traits['shy'] || $traits['anxious']) return -10;

    return 5; // Neutral/unknown
  }

  private function scorePetCompatibility($traits, $hasOtherPets, $currentPets)
  {
    if (!$hasOtherPets) {
      // No other pets - neutral/good score
      return 10;
    }

    $currentPetsLower = strtolower($currentPets ?? '');
    $hasDogs = str_contains($currentPetsLower, 'dog');
    $hasCats = str_contains($currentPetsLower, 'cat');

    $score = 0;

    if ($hasDogs && $traits['good_with_dogs']) $score += 5;
    if ($hasCats && $traits['good_with_cats']) $score += 5;
      
    // General pet-friendly
    if ($traits['friendly'] || $traits['social']) $score += 3;

    // Penalties
    if ($hasDogs && !$traits['good_with_dogs']) $score -= 10;
    if ($hasCats && !$traits['good_with_cats']) $score -= 10;

    return max(-10, $score);
  }

  private function scoreTemperamentMatch($traits, $preferredTemperament)
  {
    // If "any" selected, don't score this category
    if ($preferredTemperament === 'any') return 0;
      
    $temperamentMap = [
      'friendly' => $traits['friendly'] || $traits['social'],
      'calm' => $traits['calm'] || $traits['gentle'],
      'playful' => $traits['playful'] || $traits['energetic'],
      'protective' => ($traits['protective'] ?? false) || ($traits['loyal'] ?? false),
    ];

    // Direct match - FULL SCORE
    return ($temperamentMap[$preferredTemperament] ?? false) ? 5 : 0;
  }

  /**
    * Get reasons why this is a good match
  */
  private function getMatchReasons($rescue, $preferences, $household)
  {
    $reasons = [];
    $traits = $this->textAnalyzer->extractTraits($rescue->description);

    // Size match
    if ($preferences['size'] === $rescue->size || $preferences['size'] === 'any') {
      $reasons[] = "Perfect size for your home";
    }

    // Energy level
    if ($preferences['energy_level'] === 'high' && $traits['energetic']) {
      $reasons[] = "High energy to match your active lifestyle";
    } elseif ($preferences['energy_level'] === 'low' && $traits['calm']) {
      $reasons[] = "Calm temperament for a relaxed home";
    }

    // Child compatibility
    if ($household['have_children'] && $traits['good_with_children']) {
      $reasons[] = "Great with children";
    }

    // Pet compatibility
    if ($household['has_other_pets'] && ($traits['good_with_dogs'] || $traits['good_with_cats'])) {
      $reasons[] = "Gets along well with other pets";
    }

    // Temperament
    if ($traits['friendly']) {
      $reasons[] = "Friendly and loving personality";
    }

    // Default if no specific reasons
    if (empty($reasons)) {
      $reasons[] = "Good overall compatibility with your preferences";
    }

    return $reasons;
  }
}