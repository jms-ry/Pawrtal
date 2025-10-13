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
}
