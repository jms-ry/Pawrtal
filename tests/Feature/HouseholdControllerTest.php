<?php

namespace Tests\Feature;

use App\Models\Household;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
class HouseholdControllerTest extends TestCase
{
  use RefreshDatabase;

  /** Store function test cases */
  public function test_logged_user_can_store_address()
  {
    $user = User::factory()->create();

    //Assert there is a logged user(?)
    $this->actingAs($user);

    $householdData = [
      'house_structure' => 'Apartment',
      'household_members' => 3,
      'have_children' => 'true',
      'number_of_children' => 1,
      'has_other_pets' => 'true',
      'current_pets' => 'dogs',
      'number_of_current_pets' => 1,
      'user_id' => $user->id
    ];
    
    // Act
    $response = $this->post(route('households.store'), $householdData);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Household information has been created!');
    
    $this->assertDatabaseHas('households', $householdData);
  }

  public function test_guest_cannot_store_household()
  {
    $householdData = [
      'house_structure' => 'Apartment',
      'household_members' => 3,
      'have_children' => 'true',
      'number_of_children' => 1,
      'has_other_pets' => 'true',
      'current_pets' => 'dogs',
      'number_of_current_pets' => 1,
      'user_id' => 1,
    ];
    
    $response = $this->post(route('households.store'), $householdData);
    
    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('households', 0);
  }

  public function test_store_houshold_validates_required_fields()
  {
    // Frontend validation can be bypassed!
    $user = User::factory()->create();
    $this->actingAs($user);
    
    $response = $this->post(route('households.store'), []);
    
    $response->assertSessionHasErrors(['house_structure', 'household_members', 'have_children', 'has_other_pets']);
  }

  /** Update function test cases */
  public function test_logged_user_can_update_their_own_household()
  {
    $user = User::factory()->create();
    $household = Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

     $updatedHouseholdData = [
      'house_structure' => 'UpdatedApartment',
      'household_members' => 2,
      'have_children' => 'false',
      'number_of_children' => null,
      'has_other_pets' => 'false',
      'current_pets' => null,
      'number_of_current_pets' => null,
      'user_id' => $user->id
    ];

    $response = $this->put(route('households.update', $household), $updatedHouseholdData);
    
    $response->assertRedirect();
    $response->assertSessionHas('info', 'Household information has been updated!');
    $this->assertDatabaseHas('households', $updatedHouseholdData);

  }

  public function test_guest_cannot_update_household()
  {
    $household = Household::factory()->create();

    $response = $this->put(route('households.update', $household), [
      'house_structure' => 'Hacked Apartment',
    ]);

    $response->assertRedirect(route('login'));
    $this->assertDatabaseMissing('households', ['house_structure' => 'Hacked Structure']);
  }

  public function test_user_cannot_update_other_users_household()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $household = Household::factory()->create([
      'user_id' => $user2->id,
      'house_structure' => 'Original Apartment'
    ]);

    $this->actingAs($user1);
      
    $response = $this->put(route('households.update', $household), [
      'house_structure' => 'Hacked Apartment',
      'household_members' => 3,
      'have_children' => 'true',
      'number_of_children' => 1,
      'has_other_pets' => 'true',
      'current_pets' => 'dogs',
      'number_of_current_pets' => 1,
      'user_id' => $user2->id
    ]);
      
    $response->assertForbidden();
    $this->assertDatabaseHas('households', ['house_structure' => 'Original Apartment']);
  }

  public function test_update_household_validates_required_fields()
  {
    $user = User::factory()->create();

    $household = Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $updatedHouseholdData = [
      'house_structure' => null,
      'household_members' => null,
      'have_children' => null,
      'number_of_children' => null,
      'has_other_pets' => null,
      'current_pets' => null,
      'number_of_current_pets' => null,
    ];

    $response = $this->put(route('households.update', $household), $updatedHouseholdData);
    $response->assertSessionHasErrors(['house_structure', 'household_members', 'have_children', 'has_other_pets']);
  }

  public function test_updating_nonexistent_household_returns_404()
  {
    $user = User::factory()->create();
    $this->actingAs($user);
      
    $response = $this->put(route('households.update', 99999), [
      'house_structure' => 'UpdatedApartment',
      'household_members' => 2,
      'have_children' => 'false',
      'number_of_children' => null,
      'has_other_pets' => 'false',
      'current_pets' => null,
      'number_of_current_pets' => null,
    ]);
      
    $response->assertNotFound();
  }

  /** Delete function test cases */
  public function test_logged_user_can_delete_their_own_household()
  {
    $user = User::factory()->create();  
    $household = Household::factory()->create(['user_id' => $user->id]);
        
    $this->actingAs($user);
        
    $response = $this->delete(route('households.destroy', $household));
        
    $response->assertRedirect();
    $response->assertSessionHas('warning', 'Household information has been deleted!');
    $this->assertDatabaseMissing('households', ['id' => $household->id]);
  }

  public function test_guest_cannot_delete_household()
  {
    $household = Household::factory()->create();
        
    $response = $this->delete(route('addresses.destroy', $household));
        
    $response->assertRedirect(route('login'));
    $this->assertDatabaseHas('households', ['id' => $household->id]);
  }

  public function test_user_cannot_delete_others_household()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $household = Household::factory()->create(['user_id' => $user2->id]);

    $this->actingAs($user1);
        
    $response = $this->delete(route('households.destroy', $household));
        
    $response->assertForbidden();
    $this->assertDatabaseHas('households', ['id' => $household->id]);
  }

  public function test_deleting_nonexistent_household_returns_404()
  {
    $user = User::factory()->create();
    $this->actingAs($user);
        
    $response = $this->delete(route('households.destroy', 99999));
        
    $response->assertNotFound();
  }
}
