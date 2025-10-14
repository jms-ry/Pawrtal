<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
  use RefreshDatabase;

  public function test_admin_can_login_and_redirected_to_dashboard(): void
  {
    $admin = User::factory()->admin()->create();

    $this->assertEquals('admin', $admin->role);
    $this->assertTrue($admin->isAdminOrStaff());
    
    $response = $this->post('/login', [
      'email' => $admin->email,
      'password' => 'password',
    ]);
    $this->assertAuthenticated();
    $response->assertRedirect('/dashboard');
    $response->assertSessionHas('success', 'You are logged in as ' .$admin->getRole() .'!');
    // if($admin->isAdminOrStaff()){
    //   $response->assertRedirect('/dashboard');
    //   $response->assertSessionHas('success', 'You are logged in as ' .$admin->getRole());
    // }
  }

  public function test_regular_user_can_login_and_redirected_to_root_page()
  {
    $user = User::factory()->create();

    $response = $this->post('/login', [
      'email' => $user->email,
      'password' => 'password',
    ]);
    $this->assertAuthenticated();
    $response->assertRedirect('/');
    $response->assertSessionHas('success', 'You logged in successfully!');
  }
  
  public function test_staff_can_login_and_redirected_to_dashboard(): void
  {
    $staff = User::factory()->staff()->create();

    $response = $this->post('/login', [
      'email' => $staff->email,
      'password' => 'password',
    ]);
    $this->assertAuthenticated();
    $response->assertRedirect('/dashboard');
    $response->assertSessionHas('success', 'You are logged in as ' .$staff->getRole().'!');
    
  }

  public function test_user_cannot_login_with_invalid_password(): void
  {
    $user = User::factory()->create();
    
    $response = $this->post('/login', [
      'email' => $user->email,
      'password' => 'wrong-password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('email');
  }
  public function test_users_can_logout(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
  }
}
