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

  /**
    * Test: Rescues with active applications are excluded from recommendations
  */
  public function test_excludes_rescues_with_active_applications()
  {
    $user = User::factory()->create();
    $user->household()->create([
      'house_structure' => 'house',
      'household_members' => 2,
      'have_children' => false,
      'has_other_pets' => false,
    ]);

    // Create 3 rescues that match user preferences
    $rescue1 = Rescue::factory()->create([
      'name' => 'Buddy',
      'size' => 'medium',
      'age' => '2 years old',
      'sex' => 'male',
      'description' => 'Friendly and energetic dog.',
      'adoption_status' => 'available',
    ]);

    $rescue2 = Rescue::factory()->create([
      'name' => 'Max',
      'size' => 'medium',
      'age' => '3 years old',
      'sex' => 'male',
      'description' => 'Calm and gentle dog.',
      'adoption_status' => 'available',
    ]);

    $rescue3 = Rescue::factory()->create([
      'name' => 'Luna',
      'size' => 'medium',
      'age' => '2 years old',
      'sex' => 'female',
      'description' => 'Playful and loving dog.',
      'adoption_status' => 'available',
    ]);

    // User already has an active application for rescue1 (Buddy)
    $user->adoptionApplications()->create([
      'rescue_id' => $rescue1->id,
      'status' => 'pending',
      'preferred_inspection_start_date' => now()->addDays(7),
      'preferred_inspection_end_date' => now()->addDays(14),
      'valid_id' => fake()->imageUrl(640, 480, 'documents', true),
      'supporting_documents' => [
        fake()->imageUrl(640, 480, 'documents', true),
        fake()->imageUrl(640, 480, 'documents', true)
      ],
      'reason_for_adoption' => 'I love dogs!',
    ]);

    // Get recommendations
    $response = $this->actingAs($user)->postJson('/api/recommendations/match', [
      'size' => 'medium',
      'age_preference' => 'any',
      'sex' => 'any',
      'energy_level' => 'any',
      'maintenance_level' => 'any',
    ]);

    $response->assertOk();

    $matches = $response->json('matches');
    $matchedRescueIds = collect($matches)->pluck('rescue.id')->toArray();

    // Buddy (rescue1) should NOT be in recommendations
    $this->assertNotContains($rescue1->id, $matchedRescueIds);

    // Max and Luna should be in recommendations
    $this->assertContains($rescue2->id, $matchedRescueIds);
    $this->assertContains($rescue3->id, $matchedRescueIds);

    // Should have 2 matches (not 3)
    $this->assertEquals(2, count($matches));
  }

  /**
    * Test: Only excludes rescues with active application statuses
  */
  public function test_only_excludes_active_application_statuses()
  {
    $user = User::factory()->create();
    $user->household()->create([
      'house_structure' => 'house',
      'household_members' => 2,
      'have_children' => false,
      'has_other_pets' => false,
    ]);

    $rescue1 = Rescue::factory()->create([
      'size' => 'medium',
      'age' => '2 years old',
      'description' => 'Friendly dog.',
      'adoption_status' => 'available',
    ]);

    $rescue2 = Rescue::factory()->create([
      'size' => 'medium',
      'age' => '2 years old',
      'description' => 'Playful dog.',
      'adoption_status' => 'available',
    ]);

    // User has REJECTED application for rescue1 (should still show in recommendations)
    $user->adoptionApplications()->create([
      'rescue_id' => $rescue1->id,
      'status' => 'rejected',
      'preferred_inspection_start_date' => now()->addDays(7),
      'preferred_inspection_end_date' => now()->addDays(14),
      'valid_id' => fake()->imageUrl(640, 480, 'documents', true),
      'supporting_documents' => [
        fake()->imageUrl(640, 480, 'documents', true),
        fake()->imageUrl(640, 480, 'documents', true)
      ],
      'reason_for_adoption' => 'I love dogs!',
    ]);

    // User has CANCELLED application for rescue2 (should still show in recommendations)
    $user->adoptionApplications()->create([
      'rescue_id' => $rescue2->id,
      'status' => 'cancelled',
      'preferred_inspection_start_date' => now()->addDays(7),
      'preferred_inspection_end_date' => now()->addDays(14),
      'valid_id' => fake()->imageUrl(640, 480, 'documents', true),
      'supporting_documents' => [
        fake()->imageUrl(640, 480, 'documents', true),
        fake()->imageUrl(640, 480, 'documents', true)
      ],
      'reason_for_adoption' => 'I love dogs!',
    ]);

    // Get recommendations
    $response = $this->actingAs($user)->postJson('/api/recommendations/match', [
      'size' => 'medium',
      'age_preference' => 'any',
      'sex' => 'any',
      'energy_level' => 'any',
      'maintenance_level' => 'any',
    ]);

    $response->assertOk();

    $matches = $response->json('matches');
    $matchedRescueIds = collect($matches)->pluck('rescue.id')->toArray();

    // Both rescues should appear (rejected and cancelled are not active)
    $this->assertContains($rescue1->id, $matchedRescueIds);
    $this->assertContains($rescue2->id, $matchedRescueIds);

    $this->assertEquals(2, count($matches));
  }

  /**
    * Test: Multiple active applications exclude all applicable rescues
  */
  public function test_excludes_all_rescues_with_active_applications()
  {
    $user = User::factory()->create();
    $user->household()->create([
      'house_structure' => 'house',
      'household_members' => 2,
      'have_children' => false,
      'has_other_pets' => false,
    ]);

    $rescue1 = Rescue::factory()->create([
      'size' => 'medium',
      'age' => '2 years old',
      'description' => 'Friendly dog.',
      'adoption_status' => 'available',
    ]);

    $rescue2 = Rescue::factory()->create([
      'size' => 'medium',
      'age' => '2 years old',
      'description' => 'Playful dog.',
      'adoption_status' => 'available',
    ]);

    $rescue3 = Rescue::factory()->create([
      'size' => 'medium',
      'age' => '2 years old',
      'description' => 'Calm dog.',
      'adoption_status' => 'available',
    ]);

    // User has pending application for rescue1
    $user->adoptionApplications()->create([
      'rescue_id' => $rescue1->id,
      'status' => 'pending',
      'preferred_inspection_start_date' => now()->addDays(7),
      'preferred_inspection_end_date' => now()->addDays(14),
      'valid_id' => fake()->imageUrl(640, 480, 'documents', true),
      'supporting_documents' => [
        fake()->imageUrl(640, 480, 'documents', true),
        fake()->imageUrl(640, 480, 'documents', true)
      ],
      'reason_for_adoption' => 'I love dogs!',
    ]);

    // User has under_review application for rescue2
    $user->adoptionApplications()->create([
      'rescue_id' => $rescue2->id,
      'status' => 'under_review',
      'preferred_inspection_start_date' => now()->addDays(7),
      'preferred_inspection_end_date' => now()->addDays(14),
      'valid_id' => fake()->imageUrl(640, 480, 'documents', true),
      'supporting_documents' => [
        fake()->imageUrl(640, 480, 'documents', true),
        fake()->imageUrl(640, 480, 'documents', true)
      ],
      'reason_for_adoption' => 'I love dogs!',
    ]);

    // Get recommendations
    $response = $this->actingAs($user)->postJson('/api/recommendations/match', [
      'size' => 'medium',
      'age_preference' => 'any',
      'sex' => 'any',
      'energy_level' => 'any',
      'maintenance_level' => 'any',
    ]);

    $response->assertOk();

    $matches = $response->json('matches');
    $matchedRescueIds = collect($matches)->pluck('rescue.id')->toArray();

    // rescue1 and rescue2 should NOT be in recommendations
    $this->assertNotContains($rescue1->id, $matchedRescueIds);
     $this->assertNotContains($rescue2->id, $matchedRescueIds);

    // Only rescue3 should be in recommendations
    $this->assertContains($rescue3->id, $matchedRescueIds);
    $this->assertEquals(1, count($matches));
  }
}