<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Address;

class AddressControllerTest extends TestCase
{
  use RefreshDatabase;
  
  /** Store function test cases*/
  public function test_can_store_address()
  {
    $user = User::factory()->create();

    //Assert there is a logged user(?)
    $this->actingAs($user);

    $addressData = [
      'barangay' => 'Barangay Lahug',
      'municipality' => 'Cebu City',
      'province' => 'Cebu',
      'zip_code' => '6000',
      'user_id' => $user->id,
    ];
    // Act
    $response = $this->post(route('addresses.store'), $addressData);
    
    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Address information has been created!');
    
    $this->assertDatabaseHas('addresses', $addressData);
  }

  public function test_guest_cannot_store_address()
  {
    $addressData = [
      'barangay' => 'Barangay Lahug',
      'municipality' => 'Cebu City',
      'province' => 'Cebu',
      'zip_code' => '6000',
      'user_id' => 1,
    ];
    
    $response = $this->post(route('addresses.store'), $addressData);
    
    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('addresses', 0);
  }

  public function test_store_address_validates_required_fields()
  {
    // Frontend validation can be bypassed!
    $user = User::factory()->create();
    $this->actingAs($user);
    
    $response = $this->post(route('addresses.store'), []);
    
    $response->assertSessionHasErrors(['barangay', 'municipality', 'province', 'zip_code']);
  }

  public function test_store_address_validates_zip_code_must_be_four_digits()
  {
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Test too short
    $response = $this->post(route('addresses.store'), [
      'barangay' => 'Lahug',
      'municipality' => 'Cebu City',
      'province' => 'Cebu',
      'zip_code' => '600', // Only 3 digits
      'user_id' => $user->id,
    ]);
    
    $response->assertSessionHasErrors('zip_code');
    
    // Test too long
    $response = $this->post(route('addresses.store'), [
      'barangay' => 'Lahug',
      'municipality' => 'Cebu City',
      'province' => 'Cebu',
      'zip_code' => '60000', // 5 digits
      'user_id' => $user->id,
    ]);
    
    $response->assertSessionHasErrors('zip_code');
    
    // Test non-numeric
    $response = $this->post(route('addresses.store'), [
      'barangay' => 'Lahug',
      'municipality' => 'Cebu City',
      'province' => 'Cebu',
      'zip_code' => 'ABCD', // Letters
      'user_id' => $user->id,
    ]);
    
    $response->assertSessionHasErrors('zip_code');
  }

  /** Update function test cases */

  public function test_authenticated_user_can_update_their_own_address()
  {
    $user = User::factory()->create();
    $address = Address::factory()->create(['user_id' => $user->id]);
    
    $this->actingAs($user);
    
    $updatedData = [
      'barangay' => 'Updated Barangay',
      'municipality' => 'Updated Municipality',
      'province' => 'Updated Province',
      'zip_code' => '6001',
      'user_id' => $user->id,
    ];
    
    $response = $this->put(route('addresses.update', $address), $updatedData);
    
    $response->assertRedirect();
    $response->assertSessionHas('info', 'Address information has been updated!');
    $this->assertDatabaseHas('addresses', $updatedData);
  }

  public function test_guest_cannot_update_address()
  {
    $address = Address::factory()->create();
      
    $response = $this->put(route('addresses.update', $address), [
      'barangay' => 'Hacked Barangay',
    ]);
      
    $response->assertRedirect(route('login'));
    $this->assertDatabaseMissing('addresses', ['barangay' => 'Hacked Barangay']);
  }

  public function test_user_cannot_update_another_users_address()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $address = Address::factory()->create([
      'user_id' => $user2->id,
      'barangay' => 'Original Barangay'
    ]);
      
    $this->actingAs($user1);
      
    $response = $this->put(route('addresses.update', $address), [
      'barangay' => 'Hacked Barangay',
      'municipality' => 'Cebu City',
      'province' => 'Cebu',
      'zip_code' => '6000',
      'user_id' => $user2->id,
    ]);
      
    $response->assertForbidden();
    $this->assertDatabaseHas('addresses', ['barangay' => 'Original Barangay']);
  }

  public function test_update_address_validates_required_fields()
  {
    $user = User::factory()->create();
    $address = Address::factory()->create(['user_id' => $user->id]);
    
    $this->actingAs($user);
    
    $response = $this->put(route('addresses.update', $address), [
      'barangay' => '', // Empty required field
    ]);
    
    $response->assertSessionHasErrors(['barangay', 'municipality', 'province', 'zip_code']);
  }

  public function test_update_address_validates_zip_code_format()
  {
    $user = User::factory()->create();
    $address = Address::factory()->create(['user_id' => $user->id]);
      
    $this->actingAs($user);
      
    $response = $this->put(route('addresses.update', $address), [
      'barangay' => 'Lahug',
      'municipality' => 'Cebu City',
      'province' => 'Cebu',
      'zip_code' => 'invalid', // Invalid format
      'user_id' => $user->id,
    ]);
      
    $response->assertSessionHasErrors('zip_code');
  }

  public function test_updating_nonexistent_address_returns_404()
  {
    $user = User::factory()->create();
    $this->actingAs($user);
      
    $response = $this->put(route('addresses.update', 99999), [
      'barangay' => 'Test',
      'municipality' => 'Test',
      'province' => 'Test',
      'zip_code' => '6000',
    ]);
      
    $response->assertNotFound();
  }

  /** Destroy function test cases */

  public function test_authenticated_user_can_delete_their_own_address()
  {
    $user = User::factory()->create();
    $address = Address::factory()->create(['user_id' => $user->id]);
        
    $this->actingAs($user);
        
    $response = $this->delete(route('addresses.destroy', $address));
        
    $response->assertRedirect();
    $response->assertSessionHas('warning', 'Address information has been deleted!');
    $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
  }

 public function test_guest_cannot_delete_address()
  {
    $address = Address::factory()->create();
        
    $response = $this->delete(route('addresses.destroy', $address));
        
    $response->assertRedirect(route('login'));
    $this->assertDatabaseHas('addresses', ['id' => $address->id]);
  }
    
  public function test_user_cannot_delete_another_users_address()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $address = Address::factory()->create(['user_id' => $user2->id]);
        
    $this->actingAs($user1);
        
    $response = $this->delete(route('addresses.destroy', $address));
        
    $response->assertForbidden();
    $this->assertDatabaseHas('addresses', ['id' => $address->id]);
  }
    
  public function test_deleting_nonexistent_address_returns_404()
  {
    $user = User::factory()->create();
    $this->actingAs($user);
        
    $response = $this->delete(route('addresses.destroy', 99999));
        
    $response->assertNotFound();
  }
}
