<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class StoreUserControllerTest extends TestCase
{
  use RefreshDatabase;

  public function test_admin_can_create_staff_account(): void
  {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post('/users', [
      'first_name' => 'John',
      'last_name' => 'Doe',
      'email' => 'john.doe@example.com',
      'contact_number' => '09123456789',
      'password' => 'password123',
      'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Staff account created successfully.');
    $this->assertDatabaseHas('users', [
      'email' => 'john.doe@example.com',
      'role' => 'staff',
    ]);
  }

  public function test_staff_cannot_create_staff_account(): void
  {
    $staff = User::factory()->create(['role' => 'staff']);

    $response = $this->actingAs($staff)->post('/users', [
      'first_name' => 'John',
      'last_name' => 'Doe',
      'email' => 'john.doe@example.com',
      'contact_number' => '09123456789',
      'password' => 'password123',
      'password_confirmation' => 'password123',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('users', [
      'email' => 'john.doe@example.com',
    ]);
  }

  public function test_regular_user_cannot_create_staff_account(): void
  {
    $user = User::factory()->create(['role' => 'regular_user']);

    $response = $this->actingAs($user)->post('/users', [
      'first_name' => 'John',
      'last_name' => 'Doe',
      'email' => 'john.doe@example.com',
      'contact_number' => '09123456789',
      'password' => 'password123',
      'password_confirmation' => 'password123',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('users', [
      'email' => 'john.doe@example.com',
    ]);
  }

  public function test_unauthenticated_user_cannot_create_staff_account(): void
  {
    $response = $this->post('/users', [
      'first_name' => 'John',
      'last_name' => 'Doe',
      'email' => 'john.doe@example.com',
      'contact_number' => '09123456789',
      'password' => 'password123',
      'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/login');
    $this->assertDatabaseMissing('users', [
      'email' => 'john.doe@example.com',
    ]);
  }

  public function test_store_fails_with_missing_required_fields(): void
  {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post('/users', []);

    $response->assertSessionHasErrors([
      'first_name',
      'last_name',
      'email',
      'contact_number',
      'password',
    ]);
  }

  public function test_store_fails_with_duplicate_email(): void
  {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->actingAs($admin)->post('/users', [
      'first_name' => 'John',
      'last_name' => 'Doe',
      'email' => 'existing@example.com',
      'contact_number' => '09123456789',
      'password' => 'password123',
      'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);
  }

  public function test_store_fails_with_password_mismatch(): void
  {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post('/users', [
      'first_name' => 'John',
      'last_name' => 'Doe',
      'email' => 'john.doe@example.com',
      'contact_number' => '09123456789',
      'password' => 'password123',
      'password_confirmation' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors(['password']);
  }

  public function test_store_fails_with_password_less_than_8_characters(): void
  {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post('/users', [
      'first_name' => 'John',
      'last_name' => 'Doe',
      'email' => 'john.doe@example.com',
      'contact_number' => '09123456789',
      'password' => 'pass',
      'password_confirmation' => 'pass',
    ]);

    $response->assertSessionHasErrors(['password']);
  }

  public function test_created_staff_account_has_correct_role(): void
  {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post('/users', [
      'first_name' => 'John',
      'last_name' => 'Doe',
      'email' => 'john.doe@example.com',
      'contact_number' => '09123456789',
      'password' => 'password123',
      'password_confirmation' => 'password123',
    ]);

    $this->assertDatabaseHas('users', [
      'email' => 'john.doe@example.com',
      'role' => 'staff',
    ]);
  }
}
