<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Address;
use App\Models\Household;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
  use RefreshDatabase;

  /**
    * Test user can update their own profile
  */
  public function test_user_can_update_their_own_profile()
  {
    $user = User::factory()->create([
      'first_name' => 'John',
      'last_name' => 'Doe',
      'email' => 'john@example.com',
    ]);

    $this->actingAs($user);

    $response = $this->put(route('users.update', $user), [
      'first_name' => 'Jane',
      'last_name' => 'Smith',
      'email' => 'jane@example.com',
      'contact_number' => '09171234567',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('info', 'User profile has been updated!');

    $this->assertDatabaseHas('users', [
      'id' => $user->id,
      'first_name' => 'Jane',
      'last_name' => 'Smith',
      'email' => 'jane@example.com',
      'contact_number' => '09171234567',
    ]);
  }

  /**
    * Test user can update password
  */
  public function test_user_can_update_password()
  {
    $user = User::factory()->create([
      'password' => 'oldpassword',
    ]);

    $this->actingAs($user);

    $response = $this->put(route('users.update', $user), [
      'first_name' => $user->first_name,
      'last_name' => $user->last_name,
      'email' => $user->email,
      'password' => 'newpassword123',
      'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('info', 'User profile has been updated!');

    // Verify password was updated
    $user->refresh();
    $this->assertTrue(Hash::check('newpassword123', $user->password));
  }

  /**
    * Test password is not updated when not provided
  */
  public function test_password_not_updated_when_not_provided()
  {
    $originalPassword = Hash::make('originalpassword');
        
    $user = User::factory()->create([
      'password' => $originalPassword,
    ]);

    $this->actingAs($user);

    $response = $this->put(route('users.update', $user), [
      'first_name' => 'Updated Name',
      'email' => $user->email,
    ]);

     $response->assertRedirect();

    // Verify password remained unchanged
    $user->refresh();
    $this->assertEquals($originalPassword, $user->password);
  }

  /**
    * Test password requires confirmation
  */
  public function test_password_requires_confirmation()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->put(route('users.update', $user), [
      'first_name' => $user->first_name,
      'email' => $user->email,
      'password' => 'newpassword123',
      // Missing password_confirmation
    ]);

    $response->assertSessionHasErrors('password');
  }

  /**
    * Test password must be at least 8 characters
  */
  public function test_password_must_be_at_least_8_characters()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->put(route('users.update', $user), [
      'first_name' => $user->first_name,
      'email' => $user->email,
      'password' => 'short',
      'password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors('password');
  }

  /**
   * Test user cannot update another user's profile
  */
  public function test_user_cannot_update_another_users_profile()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $this->actingAs($user1);

    $response = $this->put(route('users.update', $user2), [
      'first_name' => 'Hacker',
      'email' => 'hacker@example.com',
    ]);

    $response->assertForbidden();

    // Verify user2 was not updated
    $this->assertDatabaseHas('users', [
      'id' => $user2->id,
      'first_name' => $user2->first_name,
      'email' => $user2->email,
    ]);
  }

  /**
    * Test email must be unique
  */
  public function test_email_must_be_unique()
  {
    $user1 = User::factory()->create(['email' => 'existing@example.com']);
    $user2 = User::factory()->create(['email' => 'user2@example.com']);

    $this->actingAs($user2);

    $response = $this->put(route('users.update', $user2), [
      'first_name' => $user2->first_name,
      'email' => 'existing@example.com', // Already taken by user1
    ]);

    $response->assertSessionHasErrors('email');
  }

  /**
    * Test user can keep their own email
  */
  public function test_user_can_keep_their_own_email()
  {
    $user = User::factory()->create(['email' => 'user@example.com']);

    $this->actingAs($user);

    $response = $this->put(route('users.update', $user), [
      'first_name' => 'Updated Name',
      'email' => 'user@example.com', // Same email
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('info', 'User profile has been updated!');

    $this->assertDatabaseHas('users', [
      'id' => $user->id,
      'first_name' => 'Updated Name',
      'email' => 'user@example.com',
    ]);
  }

  /**
    * Test role validation
  */
  public function test_role_must_be_valid()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->put(route('users.update', $user), [
      'first_name' => $user->first_name,
      'email' => $user->email,
      'role' => 'invalid_role',
    ]);

    $response->assertSessionHasErrors('role');
  }

  /**
    * Test valid role can be updated
  */
  public function test_valid_role_can_be_updated()
  {
    $user = User::factory()->create(['role' => 'regular_user']);

    $this->actingAs($user);

    $response = $this->put(route('users.update', $user), [
      'first_name' => $user->first_name,
      'email' => $user->email,
      'role' => 'staff',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('users', [
      'id' => $user->id,
      'role' => 'staff',
    ]);
  }

  /**
    * Test contact number can be updated
  */
  public function test_contact_number_can_be_updated()
  {
    $user = User::factory()->create(['contact_number' => '09171111111']);

    $this->actingAs($user);

    $response = $this->put(route('users.update', $user), [
      'first_name' => $user->first_name,
      'email' => $user->email,
      'contact_number' => '09172222222',
    ]);

    $response->assertRedirect();

     $this->assertDatabaseHas('users', [
      'id' => $user->id,
      'contact_number' => '09172222222',
    ]);
  }

  /**
    * Test contact number max length validation
  */
  public function test_contact_number_max_length_is_11()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->put(route('users.update', $user), [
      'first_name' => $user->first_name,
      'email' => $user->email,
      'contact_number' => '123456789012345678901',
    ]);

    $response->assertSessionHasErrors('contact_number');
  }

    
  /**
    * Test guest cannot update user
  */
  public function test_guest_cannot_update_user()
  {
    $user = User::factory()->create();

    $response = $this->put(route('users.update', $user), [
      'first_name' => 'Hacker',
      'email' => 'hacker@example.com',
    ]);

    $response->assertRedirect(route('login'));
  }

  /**
    * Test all fields can be updated together
  */
  public function test_all_fields_can_be_updated_together()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->put(route('users.update', $user), [
      'first_name' => 'New First',
      'last_name' => 'New Last',
      'email' => 'newemail@example.com',
      'contact_number' => '09171234567',
      'role' => 'staff',
      'password' => 'newpassword123',
      'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('info', 'User profile has been updated!');

    $user->refresh();

    $this->assertEquals('New First', $user->first_name);
    $this->assertEquals('New Last', $user->last_name);
    $this->assertEquals('newemail@example.com', $user->email);
    $this->assertEquals('09171234567', $user->contact_number);
    $this->assertEquals('staff', $user->role);
    $this->assertTrue(Hash::check('newpassword123', $user->password));
  }

  /**
    * Test nullable fields can be left empty
  */
  public function test_nullable_fields_can_be_left_empty()
  {
    $user = User::factory()->create([
      'first_name' => 'John',
      'last_name' => 'Doe',
    ]);

    $this->actingAs($user);

    $response = $this->put(route('users.update', $user), [
      'email' => $user->email,
      // All other fields omitted (nullable)
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('info', 'User profile has been updated!');
  }

  /**
    * Test first name max length validation
  */
  public function test_first_name_max_length_is_255()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->put(route('users.update', $user), [
      'first_name' => str_repeat('a', 256), // 256 characters
      'email' => $user->email,
    ]);

    $response->assertSessionHasErrors('first_name');
  }

  /**
    * Test last name max length validation
  */
  public function test_last_name_max_length_is_255()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->put(route('users.update', $user), [
      'last_name' => str_repeat('a', 256), // 256 characters
      'email' => $user->email,
    ]);

     $response->assertSessionHasErrors('last_name');
  }

  /**
    * Test email must be valid format
  */
  public function test_email_must_be_valid_format()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->put(route('users.update', $user), [
      'first_name' => $user->first_name,
      'email' => 'invalid-email-format',
    ]);

    $response->assertSessionHasErrors('email');
  }

  /**
    * Test email max length validation
  */
  public function test_email_max_length_is_255()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->put(route('users.update', $user), [
      'first_name' => $user->first_name,
      'email' => str_repeat('a', 300) . '@test.com', // 256 characters
    ]);

    $response->assertSessionHasErrors('email');
  }
}