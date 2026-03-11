<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DonationForceDeleteTest extends TestCase
{
  use RefreshDatabase;
  public function test_guest_user_cannot_force_delete_donation()
  {
    $donation = Donation::factory()->create();

    $response = $this->delete(route('donations.forceDelete', $donation->id));

    $response->assertRedirect(route('login'));
    $this->assertNotNull(Donation::withTrashed()->find($donation->id));
  }

  public function test_authenticated_user_can_force_delete_own_cancelled_donation()
  {
    $user = User::factory()->create();
    $donation = Donation::factory()->cancelled()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('donations.forceDelete', $donation->id));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Donation permanently deleted successfully.');
    $this->assertNull(Donation::withTrashed()->find($donation->id));
  }

  public function test_authenticated_user_can_force_delete_own_rejected_donation()
  {
    $user = User::factory()->create();
    $donation = Donation::factory()->rejected()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('donations.forceDelete', $donation->id));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Donation permanently deleted successfully.');
    $this->assertNull(Donation::withTrashed()->find($donation->id));
  }

  public function test_authenticated_user_cannot_force_delete_own_pending_donation()
  {
    $user = User::factory()->create();
    $donation = Donation::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('donations.forceDelete', $donation->id));

    $response->assertForbidden();
    $this->assertNotNull(Donation::withTrashed()->find($donation->id));
  }

  public function test_authenticated_user_cannot_force_delete_own_accepted_donation()
  {
    $user = User::factory()->create();
    $donation = Donation::factory()->accepted()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('donations.forceDelete', $donation->id));

    $response->assertForbidden();
    $this->assertNotNull(Donation::withTrashed()->find($donation->id));
  }

  public function test_authenticated_user_cannot_force_delete_others_donation()
  {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $donation = Donation::factory()->cancelled()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->delete(route('donations.forceDelete', $donation->id));

    $response->assertForbidden();
    $this->assertNotNull(Donation::withTrashed()->find($donation->id));
  }

  public function test_force_delete_non_existent_donation_returns_404()
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('donations.forceDelete', 999));

    $response->assertNotFound();
  }

  public function test_force_delete_redirects_to_user_my_donations_page()
  {
    $user = User::factory()->create();
    $donation = Donation::factory()->cancelled()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('donations.forceDelete', $donation->id));

    $response->assertRedirect(route('users.myDonations'));
  }

  public function test_force_delete_removes_donation_completely_from_database()
  {
    $user = User::factory()->create();
    $donation = Donation::factory()->cancelled()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('donations.forceDelete', $donation->id));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Donation permanently deleted successfully.');
    $this->assertNull(Donation::withTrashed()->find($donation->id));
  }

  public function test_force_delete_calls_authorization_policy()
  {
    $user = User::factory()->create();
    $donation = Donation::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)->delete(route('donations.forceDelete', $donation->id));

    // Assuming you have a policy method called 'forceDelete' for Donation
    $this->assertFalse($user->can('forceDelete', $donation));
  }
}
