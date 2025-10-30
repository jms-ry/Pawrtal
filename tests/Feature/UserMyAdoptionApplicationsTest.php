<?php

namespace Tests\Feature;

use App\Models\AdoptionApplication;
use App\Models\Rescue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

class UserMyAdoptionApplicationsTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannot_access_users_myAdoptionApplications()
  {
    $response = $this->get(route('users.myAdoptionApplications'));

    $response->assertRedirect(route('login'));
  }

  public function test_authenticated_user_can_access_users_myAdoptionApplications()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('users.myAdoptionApplications'));
    $response->assertOk();
  }

  public function test_authenticated_admin_user_cannot_access_users_myAdoptionApplications()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $response = $this->get(route('users.myAdoptionApplications'));
    $response->assertRedirect();
    $response->assertSessionHas('error', 'You are not authorized to access this page.');
  }

  public function test_authenticated_staff_user_cannot_access_users_myAdoptionApplications()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $response = $this->get(route('users.myAdoptionApplications'));
    $response->assertRedirect();
    $response->assertSessionHas('error', 'You are not authorized to access this page.');
  }

  public function test_authenticated_regular_user_can_only_view_their_own_adoption_applications()
  {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $visible = AdoptionApplication::factory()->count(2)->for($user)->create();

    $notVisible = AdoptionApplication::factory()->count(2)->for($otherUser)->create();

    $this->actingAs($user);

    $response = $this->get(route('users.myAdoptionApplications'));

    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
    $page
      ->component('User/MyAdoptionApp')
      ->where('adoptionApplications.data', function ($applications) use ($visible, $notVisible) {
        $ids = collect($applications)->pluck('id')->toArray();

        foreach ($visible as $application) {
          if (!in_array($application->id, $ids)) {
            return false;
          }
        }

        foreach ($notVisible as $application) {
          if (in_array($application->id, $ids)) {
            return false;
          }
        }
        return true;
      })
    );

  }

  public function test_searching_adoption_applications_using_reason_for_adoption_returns_results_case_insensitive() 
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $matchingApplication1 = AdoptionApplication::factory()->for($user)->create([
      'reason_for_adoption' => 'I want a pet.',
    ]);

    $matchingApplication2 = AdoptionApplication::factory()->for($user)->create([
      'reason_for_adoption' => 'i want a pet.',
    ]);

    $nonMatchingApplication = AdoptionApplication::factory()->for($user)->create([
      'reason_for_adoption' => 'Just because.',
    ]);

    // Act: Perform a GET request with a lowercase search term
    $response = $this->get(route('users.myAdoptionApplications', ['search' => 'i want a pet.']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching applications are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyAdoptionApp')
        // Check that the adoption applications prop exists and is paginated
        ->has('adoptionApplications.data')
        // Check that the correct adoption applications are shown
        ->where('adoptionApplications.data.0.reason_for_adoption', $matchingApplication1->reason_for_adoption)
        ->where('adoptionApplications.data.1.reason_for_adoption', $matchingApplication2->reason_for_adoption)
        // Ensure non-matching adoption applications is not present
      ->missing('adoptionApplications.data.2.reason_for_adoption',)
    );
  }

  public function test_searching_adoption_applications_using_status_returns_results_case_insensitive() 
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $matchingApplication1 = AdoptionApplication::factory()->for($user)->rejected()->create();

    $matchingApplication2 = AdoptionApplication::factory()->for($user)->rejected()->create();

    $nonMatchingApplication = AdoptionApplication::factory()->for($user)->create();

    // Act: Perform a GET request with a lowercase search term
    $response = $this->get(route('users.myAdoptionApplications', ['search' => 'rejected']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching applications are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyAdoptionApp')
        // Check that the adoption applications prop exists and is paginated
        ->has('adoptionApplications.data')
        // Check that the correct adoption applications are shown
        ->where('adoptionApplications.data.0.status', $matchingApplication1->status)
        ->where('adoptionApplications.data.1.status', $matchingApplication2->status)
        // Ensure non-matching adoption applications is not present
      ->missing('adoptionApplications.data.2.status',)
    );
  }

  public function test_searching_adoption_applications_using_rescue_name_returns_results_case_insensitive() 
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $rescue1 = Rescue::factory()->available()->create(['name' => 'Buddy']);
    $rescue2 = Rescue::factory()->available()->create(['name' => 'Max']);

    $matchingApplication1 = AdoptionApplication::factory()->for($user)->rejected()->create(['rescue_id' => $rescue1->id]);

    $matchingApplication2 = AdoptionApplication::factory()->for($user)->rejected()->create(['rescue_id' => $rescue1->id]);

    $nonMatchingApplication = AdoptionApplication::factory()->for($user)->create(['rescue_id' => $rescue2->id]);

    // Act: Perform a GET request with a lowercase search term
    $response = $this->get(route('users.myAdoptionApplications', ['search' => 'buddy']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching applications are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyAdoptionApp')
        // Check that the adoption applications prop exists and is paginated
        ->has('adoptionApplications.data')
        // Check that the correct adoption applications are shown
        ->where('adoptionApplications.data.0.rescue.name', $matchingApplication1->rescue->name)
        ->where('adoptionApplications.data.1.rescue.name', $matchingApplication2->rescue->name)
        // Ensure non-matching adoption applications is not present
      ->missing('adoptionApplications.data.2.rescue.name',)
    );
  }

  public function test_searching_adoption_applications_using_user_first_name_returns_results_case_insensitive() 
  {
    $user1 = User::factory()->create(['first_name' => 'Maxwell']);

    $this->actingAs($user1);

    $matchingApplication1 = AdoptionApplication::factory()->rejected()->create(['user_id' => $user1->id]);

    $matchingApplication2 = AdoptionApplication::factory()->rejected()->create(['user_id' => $user1->id]);
    // Act: Perform a GET request with a lowercase search term
    $response = $this->get(route('users.myAdoptionApplications', ['search' => 'maxwell']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching applications are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyAdoptionApp')
        // Check that the adoption applications prop exists and is paginated
        ->has('adoptionApplications.data')
        // Check that the correct adoption applications are shown
        ->where('adoptionApplications.data.0.user.first_name', $matchingApplication1->user->first_name)
      ->where('adoptionApplications.data.1.user.first_name', $matchingApplication2->user->first_name)
    );
  }

  public function test_searching_adoption_applications_using_user_last_name_returns_results_case_insensitive() 
  {
    $user = User::factory()->create(['last_name' => 'Maxwell']);

    $this->actingAs($user);

    $matchingApplication1 = AdoptionApplication::factory()->rejected()->create(['user_id' => $user->id]);

    $matchingApplication2 = AdoptionApplication::factory()->rejected()->create(['user_id' => $user->id]);

    // Act: Perform a GET request with a lowercase search term
    $response = $this->get(route('users.myAdoptionApplications', ['search' => 'maxwell']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching applications are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyAdoptionApp')
        // Check that the adoption applications prop exists and is paginated
        ->has('adoptionApplications.data')
        // Check that the correct adoption applications are shown
        ->where('adoptionApplications.data.0.user.last_name', $matchingApplication1->user->last_name)
      ->where('adoptionApplications.data.1.user.last_name', $matchingApplication2->user->last_name)
    );
  }

  public function test_search_with_no_matches_returns_empty_results()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    // Arrange: create some application that won't match the search term
    AdoptionApplication::factory()->for($user)->count(5)->approved()->create();


    // Act: visit the route with a filter that yields no matches
    $response = $this->get(route('users.myAdoptionApplications', ['search' => 'NonexistentAnimal']));

    // Assert: the Inertia response includes an empty applications list
    $response->assertInertia(fn ($page) => 
      $page->component('User/MyAdoptionApp')
        ->has('adoptionApplications.data', 0) // no matching results
      ->where('filters.search', 'NonexistentAnimal')
    );
  }

  public function test_search_filter_with_partial_reason_for_adoption_match_returns_correct_results()
  {
    $user = User::factory()->create();

    $this->actingAs($user);
    $matchingApplication1 = AdoptionApplication::factory()->for($user)->create([
      'reason_for_adoption' => 'I want a pet.',
    ]);

    $matchingApplication2 = AdoptionApplication::factory()->for($user)->create([
      'reason_for_adoption' => 'i want a dog.',
    ]);

    $nonMatchingApplication = AdoptionApplication::factory()->for($user)->create([
      'reason_for_adoption' => 'Just because.',
    ]);

    // Act: Perform a GET request with a lowercase search term
    $response = $this->get(route('users.myAdoptionApplications', ['search' => 'i want .']));

    // Assert: Response OK
    $response->assertStatus(200);

    
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyAdoptionApp')
        ->has('adoptionApplications.data')
      ->where('adoptionApplications.data', function ($applications) use ($matchingApplication1, $matchingApplication2, $nonMatchingApplication) {
        $reasons = collect($applications)->pluck('reason_for_adoption');

        return $reasons->contains($matchingApplication1->reason_for_adoption)
          && $reasons->contains($matchingApplication2->reason_for_adoption)
        && !$reasons->contains($nonMatchingApplication->reason_for_adoption);
      })
    );
  }

  public function test_search_filter_with_multiple_keywords_returns_correct_results()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $application1 = AdoptionApplication::factory()->for($user)->approved()->create([
      'reason_for_adoption' => 'I want a pet.',
    ]);

    $application2= AdoptionApplication::factory()->for($user)->approved()->create([
      'reason_for_adoption' => 'i want a dog.',
    ]);

    $application3 = AdoptionApplication::factory()->for($user)->create([
      'reason_for_adoption' => 'Just because.',
    ]);


    $response = $this->get(route('users.myAdoptionApplications', ['search' => 'i want approved']));

    $response->assertStatus(200);

    
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyAdoptionApp')
        ->has('adoptionApplications.data')
      ->where('adoptionApplications.data', function ($adoptionApplications) use ($application1, $application2, $application3) {
        $reasons = collect($adoptionApplications)->pluck('reason_for_adoption');

        return $reasons->contains($application1->reason_for_adoption)
          && $reasons->contains($application2->reason_for_adoption)
        && !$reasons->contains($application3->reason_for_adoption);
      })
    );
  }

  public function test_pending_status_filter_returns_only_pending_applications()
  { 
    $user = User::factory()->create();

    $this->actingAs($user);

    AdoptionApplication::factory()->for($user)->count(5)->create();
    AdoptionApplication::factory()->for($user)->count(6)->approved()->create();

    $response = $this->get(route('users.myAdoptionApplications', ['status' => 'pending']));

    $response->assertInertia(fn ($page) => 
      $page->component('User/MyAdoptionApp')
        ->has('adoptionApplications.data', 5) 
      ->where('filters.status', 'pending')
      ->has('adoptionApplications.data', fn (Assert $applications) =>
        $applications->each(fn (Assert $application) =>
          $application->where('status', 'pending')->etc()
        )
      )
    );
  }

  public function test_approved_status_filter_returns_only_approved_applications()
  { 
    $user = User::factory()->create();

    $this->actingAs($user);

    AdoptionApplication::factory()->for($user)->count(5)->create();
    AdoptionApplication::factory()->for($user)->count(6)->approved()->create();

    $response = $this->get(route('users.myAdoptionApplications', ['status' => 'approved']));

    $response->assertInertia(fn ($page) => 
      $page->component('User/MyAdoptionApp')
        ->has('adoptionApplications.data', 6) 
      ->where('filters.status', 'approved')
      ->has('adoptionApplications.data', fn (Assert $applications) =>
        $applications->each(fn (Assert $application) =>
          $application->where('status', 'approved')->etc()
        )
      )
    );
  }

  public function test_rejected_status_filter_returns_only_rejected_applications()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    AdoptionApplication::factory()->for($user)->count(5)->create();
    AdoptionApplication::factory()->for($user)->count(6)->rejected()->create();

    $response = $this->get(route('users.myAdoptionApplications', ['status' => 'rejected']));

    $response->assertInertia(fn ($page) => 
      $page->component('User/MyAdoptionApp')
        ->has('adoptionApplications.data', 6) 
      ->where('filters.status', 'rejected')
      ->has('adoptionApplications.data', fn (Assert $applications) =>
        $applications->each(fn (Assert $application) =>
          $application->where('status', 'rejected')->etc()
        )
      )
    );
  }

  public function test_cancelled_status_filter_returns_only_cancelled_applications()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    AdoptionApplication::factory()->for($user)->count(5)->create();
    AdoptionApplication::factory()->for($user)->count(6)->cancelled()->create();

    $response = $this->get(route('users.myAdoptionApplications', ['status' => 'cancelled']));

    $response->assertInertia(fn ($page) => 
      $page->component('User/MyAdoptionApp')
        ->has('adoptionApplications.data', 6) 
      ->where('filters.status', 'cancelled')
      ->has('adoptionApplications.data', fn (Assert $applications) =>
        $applications->each(fn (Assert $application) =>
          $application->where('status', 'cancelled')->etc()
        )
      )
    );
  }

  public function test_oldest_sort_filter_sorts_reports_in_ascending_order()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $application1 = AdoptionApplication::factory()->for($user)->create(['application_date' => now()->subDays(3)]);
    $application2 = AdoptionApplication::factory()->for($user)->create(['application_date' => now()->subDays(2)]);
    $application3 = AdoptionApplication::factory()->for($user)->create(['application_date' => now()->subDay()]);

    // Act: request with ?sort=asc
    $response = $this->get(route('users.myAdoptionApplications', ['sort' => 'asc']));

    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyAdoptionApp')
        ->has('adoptionApplications.data')
        ->where('adoptionApplications.data', function ($applications) use ($application1, $application2, $application3) {
          $dates = collect($applications)->pluck('application_date')->map(fn($date) => substr($date, 0, 10))->toArray();

          $expectedOrder = [
            $application1->application_date->toDateString(),
            $application2->application_date->toDateString(),
            $application3->application_date->toDateString(),
          ];

          return $dates === $expectedOrder;
        })
    );
  }

  public function test_newest_sort_filter_sorts_reports_in_descending_order()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $application1 = AdoptionApplication::factory()->for($user)->create(['application_date' => now()->subDays(3)]);
    $application2 = AdoptionApplication::factory()->for($user)->create(['application_date' => now()->subDays(2)]);
    $application3 = AdoptionApplication::factory()->for($user)->create(['application_date' => now()->subDay()]);

    // Act: request with ?sort=asc
    $response = $this->get(route('users.myAdoptionApplications', ['sort' => 'desc']));

    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyAdoptionApp')
        ->has('adoptionApplications.data')
        ->where('adoptionApplications.data', function ($applications) use ($application1, $application2, $application3) {
          $dates = collect($applications)->pluck('application_date')->map(fn($date) => substr($date, 0, 10))->toArray();

          $expectedOrder = [
            $application3->application_date->toDateString(),
            $application2->application_date->toDateString(),
            $application1->application_date->toDateString(),
          ];

          return $dates === $expectedOrder;
        })
    );
  }

  public function test_adoption_applications_are_ordered_by_created_at_in_descending_order_by_default()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $application1 = AdoptionApplication::factory()->for($user)->create(['application_date' => now()->subDays(3)]);
    $application2 = AdoptionApplication::factory()->for($user)->create(['application_date' => now()->subDays(2)]);
    $application3 = AdoptionApplication::factory()->for($user)->create(['application_date' => now()->subDay()]);

    $response = $this->get(route('users.myAdoptionApplications', ['sort' => 'desc']));

    $response->assertStatus(200);

    // Extract donations from Inertia props
    $props = $response->original->getData()['page']['props'];
    $applications = $props['adoptionApplications']['data'] ?? [];

    $this->assertCount(3, $applications);

    $dates = collect($applications)->pluck('application_date')->map(fn($date) => substr($date, 0, 10))->toArray();

    $expectedOrder = [
      $application3->application_date->toDateString(),
      $application2->application_date->toDateString(),
      $application1->application_date->toDateString(),
    ];


    $this->assertSame($expectedOrder, $dates, 'Adoption application should be ordered by application_date descending by default.');

  }

  public function test_multiple_filters_and_search_work_together()
  {
    // Test that multiple filters and a search works at the same time.


    $user = User::factory()->create(['first_name' => 'Maxwell']);

    $this->actingAs($user);

    AdoptionApplication::factory()->for($user)->approved()->create();

    AdoptionApplication::factory()->for($user)->count(2)->rejected()->create();
    AdoptionApplication::factory()->for($user)->count(2)->rejected()->create();
    AdoptionApplication::factory()->for($user)->count(2)->rejected()->create();

    // Act: apply filters and search query
    $response = $this->get(route('users.myAdoptionApplications', [
      'status' => 'approved',
      'search' => 'maxwell',
    ]));

    // Assert: Inertia props match
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyAdoptionApp')
        ->where('filters.status', 'approved')
        ->where('filters.search', 'maxwell')
        ->has('adoptionApplications.data', 1) 
        ->where('adoptionApplications.data.0.user.first_name', 'Maxwell')
      ->where('adoptionApplications.data.0.status', 'approved')
    );
  }

  public function test_combining_filters_respect_visibility_rules()
  {
    // Test taht trashed records still hidden for non-admin/staff users

    // Arrange
    $user = User::factory()->create(['first_name' => 'Maxwell']);

    $visible = AdoptionApplication::factory()->for($user)->approved()->count(3)->create();

    $trashed = AdoptionApplication::factory()->for($user)->approved()->trashed()->count(4)->create();
    
    $this->actingAs($user);

    $response = $this->get(route('users.myAdoptionApplications', [
      'status' => 'approved',
      'search' => 'maxwell',
      'with_trashed' => true,
    ]));

    $response->assertInertia(fn ($page) =>
      $page->component('User/MyAdoptionApp')
        ->has('adoptionApplications.data', 7) // visible + trashed
        ->where('filters.search', 'maxwell')
      ->where('filters.status', 'approved')
    );
  }

  public function test_search_and_filter_results_are_paginated_with_9_items_page()
  {
    $user = User::factory()->create(['first_name' => 'Maxwell']);

    AdoptionApplication::factory()->for($user)->approved()->count(13)->create();

    AdoptionApplication::factory()->for($user)->rejected()->count(3)->create();

    $this->actingAs($user);
    
    // Act: Perform GET with both search and filter
    $response = $this->get(route('users.myAdoptionApplications', [
      'status' => 'approved',
      'search' => 'maxwell',
    ]));

    // Assert: Response OK & paginated with 9 items only
    $response->assertStatus(200);

    $response->assertInertia(fn ($page) =>
      $page->component('User/MyAdoptionApp')
        ->where('filters.search', 'maxwell')
        ->where('filters.status', 'approved')
        ->has('adoptionApplications.data', 9) // 9 items per page
      ->has('adoptionApplications.links') // pagination links should exist
    );
  }

  public function test_query_string_parameters_are_preserved_in_pagination_links()
  {
    $user = User::factory()->create(['first_name' => 'Maxwell']);

    $this->actingAs($user);

    AdoptionApplication::factory()->for($user)->approved()->count(20)->create();

    // Act: Apply search and filter
    $response = $this->get(route('users.myAdoptionApplications', [
      'status' => 'approved',
      'search' => 'maxwell',
      'page' => 2,
    ]));

    // Assert: Check that pagination links include preserved query params
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyAdoptionApp')
        ->where('filters.search', 'maxwell')
        ->where('filters.status', 'approved')
        ->has('adoptionApplications.links') // just ensure 'links' exists
      ->where('adoptionApplications.links', function ($links) {
        // Convert to collection for easier iteration
        return collect($links)->every(function ($link) {
        // Skip "null" links (like "previous" or "next" on first/last page)
        if (empty($link['url'])) return true;

        return str_contains($link['url'], 'search=maxwell') &&
          str_contains($link['url'], 'status=approved');
        });
      })
    );
  }

  public function test_filters_are_returned_in_response()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $params = [
      'search' => 'maxwell',
      'sort' => 'asc',
      'status' => 'approved',
    ];

    $response = $this->get(route('users.myAdoptionApplications', $params));

    $response->assertInertia(fn ($page) =>
      $page->component('User/MyAdoptionApp')
        ->where('filters.search', 'maxwell')
        ->where('filters.sort', 'asc')
      ->where('filters.status', 'approved')
    );
  }

  public function test_empty_string_search_returns_all_available_applications()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $applications = AdoptionApplication::factory()->for($user)->approved()->count(5)->create();
    
    $response = $this->get(route('users.myAdoptionApplications', ['search' => '']));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyAdoptionApp')
        ->where('filters.search', null)
       ->has('adoptionApplications.data', 5)
    );
  }

  public function test_search_handles_special_characters_safely()
  {
    $user = User::factory()->create(['first_name' => "O'Malley"]);

    $this->actingAs($user);

    AdoptionApplication::factory()->for($user)->create();
    
    $response = $this->get(route('users.myAdoptionApplications', ['search' => "O'Mal"]));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyAdoptionApp')
        ->has('adoptionApplications.data')
      ->where('adoptionApplications.data.0.user.first_name', "O' Malley")
    );
  }

  public function test_requesting_page_beyond_available_pages_returns_empty_results()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    AdoptionApplication::factory()->for($user)->approved()->count(5)->create();
    
    $response = $this->get(route('users.myAdoptionApplications', ['page' => 999]));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyAdoptionApp')->has('adoptionApplications.data', 0)->etc()
    );
  }

  public function test_search_with_url_encoded_characters()
  {
    $user = User::factory()->create(['first_name' => "Max & Ruby"]);
    $this->actingAs($user);

    AdoptionApplication::factory()->for($user)->create();
    
    $response = $this->get(route('users.myAdoptionApplications', ['search' => 'Max & Ruby']));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyAdoptionApp')->has('adoptionApplications.data', 1)
    );
  }

  public function test_last_page_with_filters_shows_remaining_items()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    AdoptionApplication::factory()->for($user)->approved()->count(11)->create();
    
    $response = $this->get(route('users.myAdoptionApplications', [
      'status' => 'approved',
      'page' => 2
    ]));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyAdoptionApp')->has('adoptionApplications.data', 2) // 11 items, 9 per page = 2 on page 2
    );
  }

  public function test_extremely_long_search_string_handles_gracefully()
  {
    $user = User::factory()->create(['first_name' => "Max & Ruby"]);
    $this->actingAs($user);

    AdoptionApplication::factory()->for($user)->create();
    
    $longString = str_repeat('a', 1000);
    
    $response = $this->get(route('users.myAdoptionApplications', ['search' => $longString]));
    
    $response->assertStatus(200);
  }

  public function test_search_with_sql_injection_attempt_is_handled_safely()
  {
    $user = User::factory()->create(['first_name' => "Max & Ruby"]);

    $this->actingAs($user);

    AdoptionApplication::factory()->for($user)->create();
    
    $response = $this->get(route('users.myAdoptionApplications', [
      'search' => "'; DROP TABLE adoption_applications; --"
    ]));
    
    $response->assertStatus(200);
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyAdoptionApp')->has('adoptionApplications.data', 0)
    );
    
    // Verify the table still exists
    $this->assertDatabaseHas('adoption_applications', ['user_id' => $user->id]);
  }

  public function test_empty_search_with_other_filters_works_correctly()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    AdoptionApplication::factory()->for($user)->approved()->count(3)->create();
   AdoptionApplication::factory()->for($user)->rejected()->count(3)->create();
    
    $response = $this->get(route('users.myAdoptionApplications', [
      'search' => '',
      'status' => 'approved'
    ]));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyAdoptionApp')
        ->where('filters.search', null)
        ->where('filters.status', 'approved')
      ->has('adoptionApplications.data', 3)
    );
  }

  public function test_invalid_sort_parameter_defaults_to_desc_order()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $application1 = AdoptionApplication::factory()->approved()->for($user)->create(['application_date' => now()->subDays(2)]);
    $application2 = AdoptionApplication::factory()->approved()->for($user)->create(['application_date' => now()->subDay()]);
    
    // Invalid sort value should be ignored, defaulting to desc
    $response = $this->get(route('users.myAdoptionApplications', ['sort' => 'invalid']));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyAdoptionApp')
        ->where('filters.sort', null) // Should be null for invalid values
      ->where('adoptionApplications.data.0.id', $application2->id) // Newest first (desc)
    );
  }

  public function test_response_include_previousUrl_value()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->from('/')->get(route('users.myAdoptionApplications'));
    $response->assertOk();

    $response->assertInertia(fn ($page) =>
      $page->component('User/MyAdoptionApp')->where('previousUrl', url('/'))
    );
  }

  public function test_user_data_includes_id_role_and_fullName_for_authenticated_user()
  {
    $user = User::factory()->create([
      'first_name' => 'Freddie',
      'last_name' => 'Freeman'
    ]);

    $this->actingAs($user);

    $response = $this->get(route('users.myAdoptionApplications'));

    $response->assertInertia(fn ($page) =>
      $page->component('User/MyAdoptionApp')
        ->has('user')
        ->where('user.id', $user->id)
        ->where('user.role', $user->role)
      ->where('user.fullName', $user->fullName())
    );
  }
}
