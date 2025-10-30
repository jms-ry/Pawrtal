<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class UserMyDonationsTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannot_access_users_mydonations()
  {
    $response = $this->get(route('users.myDonations'));

    $response->assertRedirect(route('login'));
  }

  public function test_authenticated_user_can_access_users_mydonations()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('users.myDonations'));
    $response->assertOk();
  }

  public function test_authenticated_user_can_only_view_their_own_donations()
  {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $visible = Donation::factory()->count(2)->for($user)->create();

    $notVisible = Donation::factory()->count(2)->for($otherUser)->create();

    $this->actingAs($user);

    $response = $this->get(route('users.myDonations'));

    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
    $page
      ->component('User/MyDonations')
      ->where('donations.data', function ($donations) use ($visible, $notVisible) {
        $ids = collect($donations)->pluck('id')->toArray();

        foreach ($visible as $donation) {
          if (!in_array($donation->id, $ids)) {
            return false;
          }
        }

        foreach ($notVisible as $donation) {
          if (in_array($donation->id, $ids)) {
            return false;
          }
        }
        return true;
      })
    );

  }

  public function test_searching_donations_using_item_description_returns_results_case_insensitive() 
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $matchingDonation1 = donation::factory()->for($user)->create([
      'item_description' => 'Food donation',
    ]);

    $matchingDonation2 = donation::factory()->for($user)->create([
      'item_description' => 'food donation',
    ]);

    $nonMatchingDonation = donation::factory()->for($user)->create([
      'item_description' => 'clothing',
    ]);

    // Act: Perform a GET request with a lowercase search term
    $response = $this->get(route('users.myDonations', ['search' => 'food donation']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching donations are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyDonations')
        // Check that the donations prop exists and is paginated
        ->has('donations.data')
        // Check that the correct donations are shown
        ->where('donations.data.0.item_description', $matchingDonation1->item_description)
        ->where('donations.data.1.item_description', $matchingDonation2->item_description)
        // Ensure non-matching donation is not present
      ->missing('donations.data.2.item_description',)
    );
  }

  public function test_searching_donations_using_contact_person_returns_results_case_insensitive() 
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $matchingDonation1 = donation::factory()->for($user)->create([
      'contact_person' => 'Maxwell Cowell',
    ]);

    $matchingDonation2 = donation::factory()->for($user)->create([
      'contact_person' => 'maxwell cowell',
    ]);

    $nonMatchingDonation = donation::factory()->for($user)->create([
      'contact_person' => 'Alex',
    ]);

    // Act: Perform a GET request with a lowercase search term
    $response = $this->get(route('users.myDonations', ['search' => 'maxwell cowell']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching donations are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyDonations')
        // Check that the donations prop exists and is paginated
        ->has('donations.data')
        // Check that the correct donations are shown
        ->where('donations.data.0.contact_person', $matchingDonation1->contact_person)
        ->where('donations.data.1.contact_person', $matchingDonation2->contact_person)
        // Ensure non-matching donation is not present
      ->missing('donations.data.2.contact_person',)
    );
  }

  public function test_searching_donations_using_pick_up_location_returns_results_case_insensitive() 
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $matchingDonation1 = donation::factory()->for($user)->create([
      'pick_up_location' => 'New York Yankees Stadium',
    ]);

    $matchingDonation2 = donation::factory()->for($user)->create([
      'pick_up_location' => 'new york yankees stadium',
    ]);

    $nonMatchingDonation = donation::factory()->for($user)->create([
      'pick_up_location' => 'Los Angeles Park',
    ]);

    // Act: Perform a GET request with a lowercase search term
    $response = $this->get(route('users.myDonations', ['search' => 'new york yankees stadium']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching donations are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyDonations')
        // Check that the donations prop exists and is paginated
        ->has('donations.data')
        // Check that the correct donations are shown
        ->where('donations.data.0.pick_up_location', $matchingDonation1->pick_up_location)
        ->where('donations.data.1.pick_up_location', $matchingDonation2->pick_up_location)
        // Ensure non-matching donation is not present
      ->missing('donations.data.2.pick_up_location',)
    );
  }

  public function test_searching_donations_using_status_returns_results_case_insensitive() 
  {
    $user = User::factory()->create();

    $this->actingAs($user);
    
    $matchingDonation1 = donation::factory()->for($user)->create([
      'status' => 'accepted',
    ]);

    $matchingDonation2 = donation::factory()->for($user)->create([
      'status' => 'accepted',
    ]);

    $nonMatchingDonation = donation::factory()->for($user)->create([
      'status' => 'rejected',
    ]);

    // Act: Perform a GET request with a lowercase search term
    $response = $this->get(route('users.myDonations', ['search' => 'Accepted']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching donations are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyDonations')
        // Check that the donations prop exists and is paginated
        ->has('donations.data')
        // Check that the correct donations are shown
        ->where('donations.data.0.status', $matchingDonation1->status)
        ->where('donations.data.1.status', $matchingDonation2->status)
        // Ensure non-matching donation is not present
      ->missing('donations.data.2.status',)
    );
  }

  public function test_searching_donations_using_donation_type_returns_results_case_insensitive() 
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $matchingDonation1 = Donation::factory()->for($user)->monetary()->create();

    $matchingDonation2 = Donation::factory()->for($user)->monetary()->create();

    $nonMatchingDonation = Donation::factory()->for($user)->inKind()->create();

    // Act: Perform a GET request with a lowercase search term
    $response = $this->get(route('users.myDonations', ['search' => 'monetary']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching donations are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyDonations')
        // Check that the donations prop exists and is paginated
        ->has('donations.data')
        // Check that the correct donations are shown
        ->where('donations.data.0.donation_type', $matchingDonation1->donation_type)
        ->where('donations.data.1.donation_type', $matchingDonation2->donation_type)
        // Ensure non-matching donation is not present
      ->missing('donations.data.2.donation_type',)
    );
  }

  public function test_search_with_no_matches_returns_empty_results()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    // Arrange: create some donation that won't match the search term
    Donation::factory()->for($staff)->count(5)->create(['item_description' => 'Buddy']);

    // Act: visit the index route with a filter that yields no matches
    $response = $this->get(route('users.myDonations', ['search' => 'NonexistentAnimal']));

    // Assert: the Inertia response includes an empty Donations list
    $response->assertInertia(fn ($page) => 
      $page->component('User/MyDonations')
        ->has('donations.data', 0) // no matching Donations
      ->where('filters.search', 'NonexistentAnimal')
    );
  }

  public function test_search_filter_with_partial_contact_person_match_returns_correct_results()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $matchingDonation1 = Donation::factory()->for($staff)->create(['contact_person' => 'Buddy']);
    $matchingDonation2 = Donation::factory()->for($staff)->create(['contact_person' => 'buddy']);
    $nonMatchingDonation = Donation::factory()->for($staff)->create(['contact_person' => 'meow']);

    $response = $this->get(route('users.myDonations', ['search' => 'bud']));

    $response->assertStatus(200);

    
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyDonations')
        ->has('donations.data')
      ->where('donations.data', function ($donations) use ($matchingDonation1, $matchingDonation2, $nonMatchingDonation) {
        $names = collect($donations)->pluck('contact_person');

        return $names->contains($matchingDonation1->contact_person)
          && $names->contains($matchingDonation2->contact_person)
        && !$names->contains($nonMatchingDonation->contact_person);
      })
    );
  }

  public function test_search_filter_with_multiple_keywords_returns_correct_results()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $donation1 = Donation::factory()->for($staff)->create(['contact_person' => 'Buddy', 'item_description' => 'Dog Food', 'status' => 'pending']);
    $donation2 = Donation::factory()->for($staff)->create(['contact_person' => 'Max', 'item_description' => 'Cat Food', 'status' => 'rejected']);
    $donation3 = Donation::factory()->for($staff)->create(['contact_person' => 'Brian', 'item_description' => 'Dog Vitamins', 'status' => 'accepted']);

    $response = $this->get(route('users.myDonations', ['search' => 'Buddy Dog Food Pending']));

    $response->assertStatus(200);

    
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyDonations')
        ->has('donations.data')
      ->where('donations.data', function ($donations) use ($donation1, $donation2, $donation3) {
        $names = collect($donations)->pluck('contact_person');

        return $names->contains($donation1->contact_person)
          && !$names->contains($donation2->contact_person)
        && !$names->contains($donation3->contact_person);
      })
    );
  }

  public function test_in_kind_donation_type_filter_returns_only_in_kind_donations()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    Donation::factory()->for($staff)->count(5)->inKind()->create();
    Donation::factory()->for($staff)->count(6)->monetary()->create();

    $response = $this->get(route('users.myDonations', ['donation_type' => 'in-kind']));

    $response->assertInertia(fn ($page) => 
      $page->component('User/MyDonations')
        ->has('donations.data', 5) 
      ->where('filters.donation_type', 'in-kind')
      ->has('donations.data', fn (Assert $donations) =>
        $donations->each(fn (Assert $donation) =>
          $donation->where('donation_type', 'in-kind')->etc()
        )
      )
    );
  }

  public function test_monetary_donation_type_filter_returns_only_monetary_donations()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    Donation::factory()->for($staff)->count(5)->inKind()->create();
    Donation::factory()->for($staff)->count(6)->monetary()->create();

    $response = $this->get(route('users.myDonations', ['donation_type' => 'monetary']));

    $response->assertInertia(fn ($page) => 
      $page->component('User/MyDonations')
        ->has('donations.data', 6) 
      ->where('filters.donation_type', 'monetary')
      ->has('donations.data', fn (Assert $donations) =>
        $donations->each(fn (Assert $donation) =>
          $donation->where('donation_type', 'monetary')->etc()
        )
      )
    );
  }

  public function test_pending_status_filter_returns_only_pending_donations()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    Donation::factory()->for($staff)->count(5)->inKind()->create();
    Donation::factory()->for($staff)->count(6)->monetary()->accepted()->create();

    $response = $this->get(route('users.myDonations', ['status' => 'pending']));

    $response->assertInertia(fn ($page) => 
      $page->component('User/MyDonations')
        ->has('donations.data', 5) 
      ->where('filters.status', 'pending')
      ->has('donations.data', fn (Assert $donations) =>
        $donations->each(fn (Assert $donation) =>
          $donation->where('status', 'pending')->etc()
        )
      )
    );
  }

  public function test_accepted_status_filter_returns_only_pending_donations()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    Donation::factory()->for($staff)->count(5)->inKind()->accepted()->create();
    Donation::factory()->for($staff)->count(6)->monetary()->create();

    $response = $this->get(route('users.myDonations', ['status' => 'accepted']));

    $response->assertInertia(fn ($page) => 
      $page->component('User/MyDonations')
        ->has('donations.data',5) 
      ->where('filters.status', 'accepted')
      ->has('donations.data', fn (Assert $donations) =>
        $donations->each(fn (Assert $donation) =>
          $donation->where('status', 'accepted')->etc()
        )
      )
    );
  }

  public function test_rejected_status_filter_returns_only_pending_donations()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    Donation::factory()->for($staff)->count(5)->inKind()->rejected()->create();
    Donation::factory()->for($staff)->count(6)->monetary()->create();

    $response = $this->get(route('users.myDonations', ['status' => 'rejected']));

    $response->assertInertia(fn ($page) => 
      $page->component('User/MyDonations')
        ->has('donations.data',5) 
      ->where('filters.status', 'rejected')
      ->has('donations.data', fn (Assert $donations) =>
        $donations->each(fn (Assert $donation) =>
          $donation->where('status', 'rejected')->etc()
        )
      )
    );
  }

  public function test_cancelled_status_filter_returns_only_pending_donations()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    Donation::factory()->count(5)->for($staff)->inKind()->cancelled()->create();
    Donation::factory()->count(6)->for($staff)->monetary()->create();

    $response = $this->get(route('users.myDonations', ['status' => 'cancelled']));

    $response->assertInertia(fn ($page) => 
      $page->component('User/MyDonations')
        ->has('donations.data',5) 
      ->where('filters.status', 'cancelled')
      ->has('donations.data', fn (Assert $donations) =>
        $donations->each(fn (Assert $donation) =>
          $donation->where('status', 'cancelled')->etc()
        )
      )
    );
  }

  public function test_oldest_sort_filter_sorts_donations_in_ascending_order()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $donation1 = Donation::factory()->for($staff)->create(['donation_date' => now()->subDays(3)]);
    $donation2 = Donation::factory()->for($staff)->create(['donation_date' => now()->subDays(2)]);
    $donation3 = Donation::factory()->for($staff)->create(['donation_date' => now()->subDay()]);

    // Act: request with ?sort=asc
    $response = $this->get(route('users.myDonations', ['sort' => 'asc']));

    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyDonations')
        ->has('donations.data')
        ->where('donations.data', function ($donations) use ($donation1, $donation2, $donation3) {
          $dates = collect($donations)->pluck('donation_date')->map(fn($date) => substr($date, 0, 10))->toArray();

          $expectedOrder = [
            $donation1->donation_date->toDateString(),
            $donation2->donation_date->toDateString(),
            $donation3->donation_date->toDateString(),
          ];

          return $dates === $expectedOrder;
        })
    );
  }

  public function test_newest_sort_filter_sorts_donations_in_descending_order()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $donation1 = Donation::factory()->for($staff)->create(['donation_date' => now()->subDays(3)]);
    $donation2 = Donation::factory()->for($staff)->create(['donation_date' => now()->subDays(2)]);
    $donation3 = Donation::factory()->for($staff)->create(['donation_date' => now()->subDay()]);

    // Act: request with ?sort=asc
    $response = $this->get(route('users.myDonations', ['sort' => 'desc']));

    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyDonations')
        ->has('donations.data')
        ->where('donations.data', function ($donations) use ($donation1, $donation2, $donation3) {
          $dates = collect($donations)->pluck('donation_date')->map(fn($date) => substr($date, 0, 10))->toArray();

          $expectedOrder = [
            $donation3->donation_date->toDateString(),
            $donation2->donation_date->toDateString(),
            $donation1->donation_date->toDateString(),
          ];

          return $dates === $expectedOrder;
        })
    );
  }

  public function test_multiple_filters_and_search_work_together()
  {
    // Test that multiple filters and a search works at the same time.

    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    Donation::factory()->for($staff)->inKInd()->create([
      'contact_person' => 'Buddy',
      'item_description' => 'Dog Food',
      'status' => 'accepted'
    ]);

    Donation::factory()->for($staff)->count(2)->monetary()->create();
    Donation::factory()->for($staff)->count(2)->monetary()->create();
    Donation::factory()->for($staff)->count(2)->monetary()->create();

    // Act: apply filters and search query
    $response = $this->get(route('users.myDonations', [
      'donation_type' => 'in-kind',
      'status' => 'accepted',
      'search' => 'Buddy dog food',
    ]));

    // Assert: Inertia props match
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyDonations')
        ->where('filters.donation_type', 'in-kind')
        ->where('filters.status', 'accepted')
        ->where('filters.search', 'Buddy dog food')
        ->has('donations.data', 1) // only Buddy should match
        ->where('donations.data.0.contact_person', 'Buddy')
        ->where('donations.data.0.donation_type', 'in-kind')
      ->where('donations.data.0.status', 'accepted')
    );
  }

  public function test_combining_filters_respect_visibility_rules()
  {
    // Test taht trashed records still hidden for non-admin/staff users

    $admin = User::factory()->admin()->create();

    // Act & Assert for admin
    $this->actingAs($admin);
      
    // Arrange
    $visible = Donation::factory()->for($admin)->inKind()->accepted()->count(3)->create();

    $trashed = Donation::factory()->for($admin)->inKind()->accepted()->trashed()->count(4)->create();
   
    $adminResponse = $this->get(route('users.myDonations', [
      'donation_type' => 'in-kind',
      'status' => 'accepted',
      'with_trashed' => true,
    ]));

    $adminResponse->assertInertia(fn ($page) =>
      $page->component('User/MyDonations')
        ->has('donations.data', 7) // visible + trashed
        ->where('filters.donation_type', 'in-kind')
      ->where('filters.status', 'accepted')
    );
  }

  public function test_search_and_filter_results_are_paginated_with_9_items_page()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    Donation::factory()->for($staff)->monetary()->accepted()->count(15)->create();

    Donation::factory()->for($staff)->inKind()->accepted()->count(15)->create(['contact_person' => 'Buddy']);

    // Act: Perform GET with both search and filter
    $response = $this->get(route('users.myDonations', [
      'search' => 'buddy',
      'donation_type' => 'in-kind',
      'status' => 'accepted'
    ]));

    // Assert: Response OK & paginated with 9 items only
    $response->assertStatus(200);

    $response->assertInertia(fn ($page) =>
      $page->component('User/MyDonations')
        ->where('filters.search', 'buddy')
        ->where('filters.donation_type', 'in-kind')
        ->where('filters.status','accepted')
        ->has('donations.data', 9) // 9 items per page
      ->has('donations.links') // pagination links should exist
    );
  }

  public function test_query_string_parameters_are_preserved_in_pagination_links()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    Donation::factory()->for($staff)->inKind()->count(20)->create([
      'item_description' => 'Vitamins',
      'status' => 'accepted'
    ]);

    // Act: Apply search and filter
    $response = $this->get(route('users.myDonations', [
      'search' => 'vitamins',
      'status' => 'accepted',
      'page' => 2,
    ]));

    // Assert: Check that pagination links include preserved query params
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyDonations')
        ->where('filters.search', 'vitamins')
        ->where('filters.status', 'accepted')
        ->has('donations.links') // just ensure 'links' exists
      ->where('donations.links', function ($links) {
        // Convert to collection for easier iteration
        return collect($links)->every(function ($link) {
        // Skip "null" links (like "previous" or "next" on first/last page)
        if (empty($link['url'])) return true;

        return str_contains($link['url'], 'search=vitamins') &&
          str_contains($link['url'], 'status=accepted');
        });
      })
    );
  }

  public function test_filters_are_returned_in_response()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $params = [
      'search' => 'buddy',
      'donation_type' => 'in-kind',
      'sort' => 'asc',
      'status' => 'accepted',
    ];

    $response = $this->get(route('users.myDonations', $params));

    $response->assertInertia(fn ($page) =>
      $page->component('User/MyDonations')
        ->where('filters.search', 'buddy')
        ->where('filters.donation_type', 'in-kind')
        ->where('filters.sort', 'asc')
      ->where('filters.status', 'accepted')
    );
  }

  public function test_donations_are_ordered_by_created_at_in_descending_order_by_default()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $donation1 = Donation::factory()->for($staff)->create(['donation_date' => now()->subDays(3)]);
    $donation2 = Donation::factory()->for($staff)->create(['donation_date' => now()->subDays(2)]);
    $donation3 = Donation::factory()->for($staff)->create(['donation_date' => now()->subDay()]);

    $response = $this->get(route('users.myDonations'));

    // Assert response OK
    $response->assertStatus(200);

    // Extract donations from Inertia props
    $props = $response->original->getData()['page']['props'];
    $donations = $props['donations']['data'] ?? [];

    $this->assertCount(3, $donations);

    $dates = collect($donations)->pluck('donation_date')->map(fn($date) => substr($date, 0, 10))->toArray();

    $expectedOrder = [
      $donation3->donation_date->toDateString(),
      $donation2->donation_date->toDateString(),
      $donation1->donation_date->toDateString(),
    ];


    $this->assertSame($expectedOrder, $dates, 'Donations should be ordered by donation_date descending by default.');

  }
  public function test_empty_string_search_returns_all_available_donations()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $donations = Donation::factory()->for($staff)->inKind()->count(5)->create();
    
    $response = $this->get(route('users.myDonations', ['search' => '']));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyDonations')
        ->where('filters.search', null)
       ->has('donations.data', 5)
    );
  }

  public function test_search_handles_special_characters_safely()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $report = Donation::factory()->for($staff)->inKind()->create(['contact_person' => "O'Malley"]);
    
    $response = $this->get(route('users.myDonations', ['search' => "O'Mal"]));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyDonations')
        ->has('donations.data')
      ->where('donations.data.0.contact_person', "O'Malley")
    );
  }

  public function test_requesting_page_beyond_available_pages_returns_empty_results()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    Donation::factory()->for($staff)->inKind()->count(5)->create();
    
    $response = $this->get(route('users.myDonations', ['page' => 999]));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyDonations')->has('donations.data', 0)->etc()
    );
  }

  public function test_search_with_url_encoded_characters()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $donation = Donation::factory()->for($staff)->inKind()->create(['contact_person' => 'Max & Ruby']);
    
    $response = $this->get(route('users.myDonations', ['search' => 'Max & Ruby']));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyDonations')->has('donations.data', 1)
    );
  }

  public function test_last_page_with_filters_shows_remaining_items()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    Donation::factory()->for($staff)->inKind()->accepted()->count(11)->create();
    
    $response = $this->get(route('users.myDonations', [
      'donation_type' => 'in-kind',
      'status' => 'accepted',
      'page' => 2
    ]));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyDonations')->has('donations.data', 2) // 11 items, 9 per page = 2 on page 2
    );
  }

  public function test_extremely_long_search_string_handles_gracefully()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    Donation::factory()->for($staff)->inKind()->create(['contact_person' => 'Max & Ruby']);
    
    $longString = str_repeat('a', 1000);
    
    $response = $this->get(route('users.myDonations', ['search' => $longString]));
    
    $response->assertStatus(200);
  }

  public function test_search_with_sql_injection_attempt_is_handled_safely()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    Donation::factory()->for($staff)->inKind()->create(['contact_person' => 'Max & Ruby']);
    
    $response = $this->get(route('users.myDonations', [
      'search' => "'; DROP TABLE donations; --"
    ]));
    
    $response->assertStatus(200);
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyDonations')->has('donations.data', 0)
    );
    
    // Verify the table still exists
    $this->assertDatabaseHas('donations', ['contact_person' => 'Max & Ruby']);
  }

  public function test_donation_type_filter_is_case_sensitive()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    Donation::factory()->for($staff)->inKind()->count(3)->create();
    
    $response = $this->get(route('users.myDonations', ['donation_type' => 'IN-KIND']));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyDonations')
        ->where('filters.donation_type', 'IN-KIND')
      ->has('donations.data', 0) // Should return no results
    );
  }

  public function test_empty_search_with_other_filters_works_correctly()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    Donation::factory()->for($staff)->inKind()->count(3)->create();
    Donation::factory()->for($staff)->monetary()->count(2)->create();
    
    $response = $this->get(route('users.myDonations', [
      'search' => '',
      'donation_type' => 'monetary'
    ]));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyDonations')
        ->where('filters.search', null)
        ->where('filters.donation_type', 'monetary')
      ->has('donations.data', 2)
    );
  }

  public function test_invalid_sort_parameter_defaults_to_desc_order()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $donation1 = Donation::factory()->for($staff)->inKind()->create(['donation_date' => now()->subDays(2)]);
    $donation2 = Donation::factory()->for($staff)->inKind()->create(['donation_date' => now()->subDay()]);
    
    // Invalid sort value should be ignored, defaulting to desc
    $response = $this->get(route('users.myDonations', ['sort' => 'invalid']));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyDonations')
        ->where('filters.sort', null) // Should be null for invalid values
      ->where('donations.data.0.id', $donation2->id) // Newest first (desc)
    );
  }

  public function test_response_include_previousUrl_value()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $response = $this->from('/')->get(route('users.myDonations'));
    $response->assertOk();

    $response->assertInertia(fn ($page) =>
      $page->component('User/MyDonations')->where('previousUrl', url('/'))
    );
  }

  public function test_user_data_includes_id_and_fullName_for_authenticated_user()
  {
    $user = User::factory()->admin()->create([
      'first_name' => 'Freddie',
      'last_name' => 'Freeman'
    ]);

    $this->actingAs($user);

    $response = $this->get(route('users.myDonations'));

    $response->assertInertia(fn ($page) =>
      $page->component('User/MyDonations')
        ->has('user')
        ->where('user.id', $user->id)
      ->where('user.fullName', $user->fullName())
    );
  }
}
