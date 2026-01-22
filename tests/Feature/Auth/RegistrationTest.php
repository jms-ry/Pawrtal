<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
  use RefreshDatabase;

  public function test_new_users_can_register(): void
  {
    $response = $this->post('/register', [
      'first_name' => 'Test',
      'last_name' => 'User',
      'contact_number' => '09204622082',
      'email' => 'test@example.com',
      'password' => 'password',
      'password_confirmation' => 'password',
      'role' => 'regular_user',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/');
    $response->assertSessionHas('success', 'You have successfully created an account!');
  }

  public function test_new_user_cannot_register_with_existing_email(): void
  {
    // Create a user with the email first
    $this->post('/register', [
      'first_name' => 'Existing',
      'last_name' => 'User',
      'contact_number' => '09123456789',
      'email' => 'existing@email.com',
      'password' => 'password',
      'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    auth()->logout();

    // Attempt to register another user with the same email

    $response = $this->post('/register', [
      'first_name' => 'New',
      'last_name' => 'User',
      'contact_number' => '09987654321',
      'email' => 'existing@email.com',
      'password' => 'password',
      'password_confirmation' => 'password',
    ]);

    $response->assertSessionHas('error', 'An account with this email already exists. Please use a different email or try logging in.'); 
    $response->assertRedirect();

    $this->assertDatabaseCount('users', 1);

    $this->assertGuest();
  }
}
