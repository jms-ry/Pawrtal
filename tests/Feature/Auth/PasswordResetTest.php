<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
  use RefreshDatabase;

  // ========================
  // Forgot Password Page
  // ========================

  public function test_forgot_password_page_is_accessible_to_guest(): void
  {
    $response = $this->get(route('password.request'));
    $response->assertStatus(200);
  }

  public function test_forgot_password_page_is_not_accessible_to_authenticated_user(): void
  {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('password.request'));
    $response->assertRedirect();
  }

  // ========================
  // Send Reset Link
  // ========================

  public function test_reset_link_is_sent_for_existing_email(): void
  {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this->post(route('password.email'), [
      'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPassword::class);
    $response->assertSessionHas('success', 'Password reset link has been sent to your email.');
  }

  public function test_reset_link_is_not_sent_for_nonexistent_email(): void
  {
    Notification::fake();

    $response = $this->post(route('password.email'), [
      'email' => 'nonexistent@example.com',
    ]);

    Notification::assertNothingSent();
    $response->assertSessionHas('error', 'We could not find an account with that email address.');
  }

  public function test_reset_link_requires_email(): void
  {
    $response = $this->post(route('password.email'), [
      'email' => '',
    ]);

    $response->assertSessionHasErrors('email');
  }

  public function test_reset_link_requires_valid_email_format(): void
  {
    $response = $this->post(route('password.email'), [
      'email' => 'not-an-email',
    ]);

    $response->assertSessionHasErrors('email');
  }

  // ========================
  // Reset Password Page
  // ========================

  public function test_reset_password_page_is_accessible_with_valid_token(): void
  {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.email'), [
      'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
      $response = $this->get(route('password.reset', [
        'token' => $notification->token,
        'email' => $user->email,
      ]));

      $response->assertStatus(200);
      return true;
    });
  }

  // ========================
  // Reset Password Submit
  // ========================

  public function test_password_can_be_reset_with_valid_token(): void
  {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.email'), [
      'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
      $response = $this->post(route('password.update'), [
        'token' => $notification->token,
        'email' => $user->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
      ]);

      $response->assertRedirect(route('login'));
      $response->assertSessionHas('status');
      $this->assertGuest();
      return true;
    });
  }

  public function test_password_reset_requires_valid_token(): void
  {
    $user = User::factory()->create();

    $response = $this->post(route('password.update'), [
      'token' => 'invalid-token',
      'email' => $user->email,
      'password' => 'newpassword123',
      'password_confirmation' => 'newpassword123',
    ]);

    $response->assertSessionHasErrors('email');
  }

  public function test_password_reset_requires_password_confirmation(): void
  {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.email'), [
      'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
      $response = $this->post(route('password.update'), [
        'token' => $notification->token,
        'email' => $user->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'differentpassword',
      ]);

      $response->assertSessionHasErrors('password');
      return true;
    });
  }

  public function test_password_reset_requires_minimum_password_length(): void
  {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.email'), [
      'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
      $response = $this->post(route('password.update'), [
        'token' => $notification->token,
        'email' => $user->email,
        'password' => 'short',
        'password_confirmation' => 'short',
      ]);

      $response->assertSessionHasErrors('password');
      return true;
    });
  }

  public function test_authenticated_user_cannot_access_forgot_password_page(): void
  {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('password.request'));
    $response->assertRedirect();
  }
}