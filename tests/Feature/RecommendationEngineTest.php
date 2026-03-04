<?php

namespace Tests\Feature;

use App\Models\Rescue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecommendationEngineTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_cannot_access_recommendations()
  {
    $response = $this->postJson('/api/recommendations/match', [
      'size' => 'medium',
      'age_preference' => 'young',
      'sex' => 'any',
      'energy_level' => 'moderate',
      'maintenance_level' => 'moderate',
    ]);

    $response->assertUnauthorized();
  }

  public function test_authenticated_user_can_get_recommendations()
  {
    $user = User::factory()->create();
    $user->household()->create([
      'house_structure' => 'house',
      'household_members' => 3,
      'have_children' => true,
      'number_of_children' => 2,
      'has_other_pets' => false,
    ]);

    Rescue::factory()->create([
      'species' => 'dog',
      'size' => 'medium',
      'age' => '2 years old',
      'sex' => 'male',
      'description' => 'A friendly and energetic dog, great with children.',
      'adoption_status' => 'available',
    ]);

    $response = $this->actingAs($user)->postJson('/api/recommendations/match', [
      'size' => 'medium',
      'age_preference' => 'young',
      'sex' => 'any',
      'energy_level' => 'high',
      'maintenance_level' => 'moderate',
    ]);

    $response->assertOk();
    $response->assertJsonStructure([
      'success',
      'count',
      'matches' => [
        '*' => [
          'rescue',
          'score',
          'match_percentage',
          'reasons',
        ],
      ],
    ]);
  }

  public function test_returns_no_matches_when_criteria_not_met()
  {
    $user = User::factory()->create();
    $user->household()->create([
      'house_structure' => 'apartment',
      'household_members' => 1,
      'have_children' => false,
      'has_other_pets' => false,
    ]);

    // Create only large dogs
    Rescue::factory()->count(3)->create([
      'species' => 'dog',
      'size' => 'large',
      'sex' => 'female',
      'adoption_status' => 'available',
    ]);

    
    $response = $this->actingAs($user)->postJson('/api/recommendations/match', [
      'size' => 'small',
      'age_preference' => 'young',
      'sex' => 'male',
      'energy_level' => 'any',
      'maintenance_level' => 'any',
    ]);

    $response->assertOk();
    $response->assertJson([
      'success' => true,
      'count' => 0,
    ]);
  }

  public function test_validation_fails_with_invalid_preferences()
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/recommendations/match', [
      'size' => 'invalid',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['size']);
  }

  public function test_matches_are_sorted_by_score()
  {
    $user = User::factory()->create();
    $user->household()->create([
      'house_structure' => 'house',
      'household_members' => 4,
      'have_children' => true,
      'has_other_pets' => false,
    ]);

    // Create rescues with different compatibility
    $perfectMatch = Rescue::factory()->create([
      'size' => 'medium',
      'age' => '2 years',
      'sex' => 'male',
      'description' => 'Friendly, energetic, and great with children.',
      'adoption_status' => 'available',
    ]);

    $goodMatch = Rescue::factory()->create([
      'size' => 'large',
      'age' => '3 years',
      'sex' => 'female',
      'description' => 'Calm and gentle.',
      'adoption_status' => 'available',
    ]);

    $response = $this->actingAs($user)->postJson('/api/recommendations/match', [
      'size' => 'medium',
      'age_preference' => 'young',
      'sex' => 'male',
      'energy_level' => 'high',
      'maintenance_level' => 'moderate',
    ]);

    $response->assertOk();
        
    $matches = $response->json('matches');
        
    // First match should have higher score
    $this->assertGreaterThan(
      $matches[1]['score'] ?? 0,
      $matches[0]['score']
    );
  }
}