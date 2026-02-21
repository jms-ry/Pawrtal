<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class DonationSuccessTest extends TestCase
{
  use RefreshDatabase;

  /**
    * Test: Guest user is redirected to login
  */
  public function test_guest_user_is_redirected_to_login()
  {
    $response = $this->get(route('donations.success'));
        
    $response->assertRedirect(route('login'));
  }

  /**
    * Test: Authenticated user without PayMongo referer is redirected home
  */
  public function test_authenticated_user_without_paymongo_referer_redirected_to_home()
  {
    $user = User::factory()->create();
        
    $response = $this->actingAs($user)->get(route('donations.success'));
        
    $response->assertRedirect('/');
    $response->assertSessionHas('info', 'Please make a donation to view this page.');
  }

  /**
    * Test: Regular user with PayMongo referer can access page
  */
  public function test_regular_user_with_paymongo_referer_can_access()
  {
    $user = User::factory()->create(['role' => 'regular_user']);
        
    $response = $this->actingAs($user)
      ->withHeaders(['referer' => 'https://checkout.paymongo.com/sessions/cs_123'])
    ->get(route('donations.success'));
        
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) =>
      $page->component('Donate/Success')
    );
  }

  /**
    * Test: Admin user with PayMongo referer can access page
  */
  public function test_admin_user_with_paymongo_referer_can_access()
  {
    $user = User::factory()->admin()->create();
        
    $response = $this->actingAs($user)
      ->withHeaders(['referer' => 'https://checkout.paymongo.com/sessions/cs_123'])
    ->get(route('donations.success'));
        
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) =>
      $page->component('Donate/Success')
    );
  }

  /**
    * Test: Staff user with PayMongo referer can access page
  */
  public function test_staff_user_with_paymongo_referer_can_access()
  {
    $user = User::factory()->staff()->create();
        
    $response = $this->actingAs($user)
      ->withHeaders(['referer' => 'https://checkout.paymongo.com/sessions/cs_123'])
    ->get(route('donations.success'));
        
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) =>
      $page->component('Donate/Success')
    );
  }

  /**
    * Test: Donation is found by source ID
  */
  public function test_donation_is_found_by_source_id()
  {
    $user = User::factory()->create();
        
    $donation = Donation::factory()->create([
      'user_id' => $user->id,
      'donation_type' => 'monetary',
      'payment_intent_id' => 'cs_test_12345',
      'amount' => 100,
    ]);
        
    $response = $this->actingAs($user)
      ->withHeaders(['referer' => 'https://checkout.paymongo.com/'])
    ->get(route('donations.success', ['id' => 'cs_test_12345']));
        
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) =>
      $page->component('Donate/Success')
        ->has('donation')
        ->where('donation.id', $donation->id)
      ->where('donation.amount', '100')
    );
  }

  /**
    * Test: Latest donation is retrieved when no source ID provided
  */
  public function test_latest_donation_retrieved_when_no_source_id()
  {
    $user = User::factory()->create();
        
    // Create older donation
    $oldDonation = Donation::factory()->create([
      'user_id' => $user->id,
      'donation_type' => 'monetary',
      'amount' => 50,
      'donation_date' => now()->subDays(2),
      'status' => 'accepted'
    ]);
        
    // Create newer donation
    $newDonation = Donation::factory()->create([
      'user_id' => $user->id,
      'donation_type' => 'monetary',
      'amount' => 100,
      'donation_date' => now()->subDay(),
    ]);
        
    $response = $this->actingAs($user)
      ->withHeaders(['referer' => 'https://checkout.paymongo.com/'])
    ->get(route('donations.success'));
        
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) =>
      $page->component('Donate/Success')
        ->has('donation')
        ->where('donation.id', $newDonation->id)
      ->where('donation.amount', '100')
    );
  }

  /**
    * Test: Only monetary donations are retrieved (not in-kind)
  */
  public function test_only_monetary_donations_are_retrieved()
  {
    $user = User::factory()->create();
        
    // Create in-kind donation
    $inKindDonation = Donation::factory()->create([
      'user_id' => $user->id,
      'donation_type' => 'in-kind',
      'donation_date' => now(),
    ]);
        
    // Create monetary donation
    $monetaryDonation = Donation::factory()->create([
      'user_id' => $user->id,
      'donation_type' => 'monetary',
      'amount' => 100,
      'donation_date' => now()->subHour(),
    ]);
        
    $response = $this->actingAs($user)
      ->withHeaders(['referer' => 'https://checkout.paymongo.com/'])
    ->get(route('donations.success'));
        
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) =>
      $page->component('Donate/Success')
        ->has('donation')
        ->where('donation.id', $monetaryDonation->id)
      ->where('donation.donation_type', 'monetary')
    );
  }

  /**
    * Test: No donation is passed when user has no monetary donations
  */
  public function test_no_donation_when_user_has_no_monetary_donations()
  {
    $user = User::factory()->create();
        
    // Create only in-kind donation
    Donation::factory()->create([
      'user_id' => $user->id,
      'donation_type' => 'in-kind',
    ]);
        
    $response = $this->actingAs($user)
      ->withHeaders(['referer' => 'https://checkout.paymongo.com/'])
    ->get(route('donations.success'));
        
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) =>
      $page->component('Donate/Success')->where('donation', null)
    );
  }

  /**
    * Test: Source ID takes priority over latest donation
  */
  public function test_source_id_takes_priority_over_latest_donation()
  {
    $user = User::factory()->create();
        
    // Create newer donation
    $newerDonation = Donation::factory()->create([
      'user_id' => $user->id,
      'donation_type' => 'monetary',
      'payment_intent_id' => 'cs_newer',
      'amount' => 200,
      'donation_date' => now(),
    ]);
        
    // Create older donation
    $olderDonation = Donation::factory()->create([
      'user_id' => $user->id,
      'donation_type' => 'monetary',
      'payment_intent_id' => 'cs_older',
      'amount' => 100,
      'donation_date' => now()->subDay(),
    ]);
        
    // Request with older donation's source ID
    $response = $this->actingAs($user)
      ->withHeaders(['referer' => 'https://checkout.paymongo.com/'])
    ->get(route('donations.success', ['id' => 'cs_older']));
        
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) =>
      $page->component('Donate/Success')
        ->where('donation.id', $olderDonation->id)
      ->where('donation.amount', '100')
    );
  }

  /**
    * Test: Invalid source ID falls back to latest donation
  */
  public function test_invalid_source_id_falls_back_to_latest_donation()
  {
    $user = User::factory()->create();
        
    $donation = Donation::factory()->create([
      'user_id' => $user->id,
      'donation_type' => 'monetary',
      'payment_intent_id' => 'cs_valid',
      'amount' => 100,
    ]);
        
    // Request with invalid source ID
    $response = $this->actingAs($user)
      ->withHeaders(['referer' => 'https://checkout.paymongo.com/'])
    ->get(route('donations.success', ['id' => 'cs_invalid_not_found']));
        
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) =>
      $page->component('Donate/Success')->where('donation.id', $donation->id)
    );
  }

  /**
    * Test: Different PayMongo referer variations are accepted
  */
  public function test_different_paymongo_referer_variations_accepted()
  {
    $user = User::factory()->create();
        
    $referers = [
      'https://checkout.paymongo.com/sessions/cs_123',
      'https://paymongo.com/checkout',
      'http://test.paymongo.com/',
      'https://api.paymongo.com/v1/sources',
    ];
        
    foreach ($referers as $referer) {
      $response = $this->actingAs($user)
        ->withHeaders(['referer' => $referer])
      ->get(route('donations.success'));
            
      $response->assertOk();
    }
  }

  /**
    * Test: Non-PayMongo referers are rejected
  */
  public function test_non_paymongo_referers_are_rejected()
  {
    $user = User::factory()->create();
        
    $invalidReferers = [
      'https://google.com',
      'https://faekpay.com',
      'https://paymongo.fake.com',
      'https://tesst.com',
    ];
        
    foreach ($invalidReferers as $referer) {
      $response = $this->actingAs($user)
        ->withHeaders(['referer' => $referer])
      ->get(route('donations.success'));
      
      $response->assertRedirect('/');
    }
  }

  /**
    * Test: User can only see their own donations
  */
  public function test_user_cannot_see_other_users_donations_by_source_id()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    
    // Create donation for user2
    Donation::factory()->create([
      'user_id' => $user2->id,
      'donation_type' => 'monetary',
      'payment_intent_id' => 'cs_user2_donation',
      'amount' => 500,
    ]);
    
    // User1 tries to access with user2's source ID
    $response = $this->actingAs($user1)
      ->withHeaders(['referer' => 'https://checkout.paymongo.com/'])
    ->get(route('donations.success', ['id' => 'cs_user2_donation']));
    
    $response->assertOk();
    
    // Should return null (no donation found for user1)
    $response->assertInertia(fn (Assert $page) =>
      $page->component('Donate/Success')
        ->where('donation', null)
      ->etc()
    );
  }

  /**
    * Test: Empty referer header is treated as no referer
  */
  public function test_empty_referer_header_redirects_to_home()
  {
    $user = User::factory()->create();
        
    $response = $this->actingAs($user)
      ->withHeaders(['referer' => ''])
    ->get(route('donations.success'));
        
    $response->assertRedirect('/');
  }

  /**
    * Test: Page renders correctly with all donation data
  */
  /**
 * Test: Page renders correctly with all donation data
 */
  public function test_page_renders_with_complete_donation_data()
  {
    $user = User::factory()->create();
      
    $donation = Donation::factory()->create([
      'user_id' => $user->id,
      'donation_type' => 'monetary',
      'payment_intent_id' => 'cs_complete',
      'amount' => 250.50,
      'payment_method' => 'gcash',
      'payment_status' => 'paid',
      'transaction_reference' => 'pay_12345',
      'paid_at' => now(),
    ]);
      
    $response = $this->actingAs($user)
      ->withHeaders(['referer' => 'https://checkout.paymongo.com/'])
    ->get(route('donations.success', ['id' => 'cs_complete']));
      
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) =>
      $page->component('Donate/Success')
        ->has('donation')
        ->has('donation.amount')  // Just check it exists
        ->where('donation.payment_method', 'gcash')
      ->where('donation.transaction_reference', 'pay_12345')
    );
  }
}