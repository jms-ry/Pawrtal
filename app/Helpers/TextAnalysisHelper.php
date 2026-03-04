<?php

namespace App\Helpers;

class TextAnalysisHelper
{
  protected $keywords = [
    'energetic' => [
      'energetic', 'active', 'playful', 'lively', 'high energy', 
      'bouncy', 'spirited', 'athletic', 'loves to play', 'very active'
    ],
    'calm' => [
      'calm', 'gentle', 'quiet', 'relaxed', 'mellow', 'laid-back', 
      'peaceful', 'serene', 'tranquil', 'low-key', 'easygoing'
    ],
    'friendly' => [
      'friendly', 'loving', 'affectionate', 'sweet', 'kind', 
      'warm', 'sociable', 'outgoing', 'welcoming', 'amiable'
    ],
    'good_with_children' => [
      'good with children', 'child-friendly', 'patient with kids', 
      'loves children', 'great with kids', 'kid-friendly', 
      'gentle with children', 'tolerant of children'
    ],
    'good_with_dogs' => [
      'good with dogs', 'dog-friendly', 'loves other dogs', 
      'gets along with dogs', 'compatible with dogs', 'sociable with dogs'
    ],
    'good_with_cats' => [
      'good with cats', 'cat-friendly', 'gets along with cats', 
      'compatible with cats', 'likes cats'
    ],
    'playful' => [
      'playful', 'fun-loving', 'enjoys playing', 'loves toys', 
      'interactive', 'game-loving'
    ],
    'gentle' => [
      'gentle', 'soft', 'tender', 'mild-mannered', 'delicate', 
      'careful', 'easy-going'
    ],
    'patient' => [
      'patient', 'tolerant', 'understanding', 'forbearing', 'calm-natured'
    ],
    'shy' => [
      'shy', 'timid', 'reserved', 'nervous', 'cautious', 
      'wary', 'hesitant', 'introverted'
    ],
    'anxious' => [
      'anxious', 'fearful', 'worried', 'stressed', 'nervous', 'skittish'
    ],
    'protective' => [
      'protective', 'guardian', 'watchful', 'alert', 'vigilant'
    ],
    'loyal' => [
      'loyal', 'devoted', 'faithful', 'dedicated', 'committed'
    ],
    'social' => [
      'social', 'sociable', 'loves company', 'people-oriented', 
      'enjoys companionship', 'gregarious'
    ],
    'independent' => [
      'independent', 'self-sufficient', 'low-maintenance', 
      'doesn\'t need constant attention', 'content alone'
    ],
    'needs_attention' => [
      'needs attention', 'requires companionship', 'doesn\'t like being alone', 
      'seeks interaction', 'high-maintenance'
    ],
  ];

  /**
    * Extract behavioral traits from rescue description
  */
  public function extractTraits($description)
  {
    $description = strtolower($description ?? '');
        
    $traits = [];

    foreach ($this->keywords as $trait => $keywords) {
      $traits[$trait] = $this->containsKeywords($description, $keywords);
    }

    return $traits;
  }

  /**
    * Check if text contains any of the keywords
  */
  private function containsKeywords($text, $keywords)
  {
    foreach ($keywords as $keyword) {
      if (str_contains($text, strtolower($keyword))) {
        return true;
      }
    }
    return false;
  }

  /**
    * Parse age string to months
    * Examples: "2 years old" → 24, "6 months" → 6, "3 weeks" → 0.75
  */
  public function parseAge($ageString)
  {
    if (empty($ageString)) return null;

    $ageString = strtolower($ageString);

    // Match patterns like "2 years", "6 months", "3 weeks"
    if (preg_match('/(\d+(?:\.\d+)?)\s*(year|yr|month|mon|week|wk)/i', $ageString, $matches)) {
      $value = (float) $matches[1];
      $unit = strtolower($matches[2]);

      // Convert to months
      if (in_array($unit, ['year', 'yr', 'years', 'yrs'])) {
        return $value * 12;
      }
            
      if (in_array($unit, ['month', 'mon', 'months', 'mons'])) {
        return $value;
      }
            
      if (in_array($unit, ['week', 'wk', 'weeks', 'wks'])) {
        return $value / 4;
      }
    }

    return null;
  }
}