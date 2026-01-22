<?php

namespace Tests\Feature;

use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
class ReportIndexTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_can_access_report_index_page()
  {
    $response = $this->get('/reports');

    $response->assertStatus(200);
  }

  public function test_regular_user_can_access_report_index_page()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('reports.index'));
    $response->assertStatus(200);

    $response->assertViewIs('app');
  }

  public function test_admin_user_can_access_report_index_page()
  {
   $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $response = $this->get(route('reports.index'));
    $response->assertStatus(200);

    $response->assertViewIs('app');
  }

  public function test_staff_user_can_access_report_index_page()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $response = $this->get(route('reports.index'));
    $response->assertStatus(200);

    $response->assertViewIs('app');
  }

  public function test_guest_user_can_only_view_non_trashed_reports()
  {
    $nonTrashed = Report::factory()->count(3)->create();
    $trashed = Report::factory()->count(2)->trashed()->create();

    $response = $this->get(route('reports.index'));
    $response->assertStatus(200);

    foreach ($nonTrashed as $report) {
      $response->assertSee($report->animal_name);
    }

    foreach ($trashed as $report) {
      $response->assertDontSee($report->animal_name);
    }
  }

  public function test_regular_user_can_only_view_non_trashed_reports()
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    $nonTrashed = Report::factory()->count(3)->create();
    $trashed = Report::factory()->count(2)->trashed()->create();

    $response = $this->get(route('reports.index'));
    $response->assertStatus(200);

    foreach ($nonTrashed as $report) {
      $response->assertSee($report->animal_name);
    }

    foreach ($trashed as $report) {
      $response->assertDontSee($report->animal_name);
    }
  }

  public function test_regular_user_who_owns_trashed_reports_can_view_their_own_trashed_reports_and_all_non_trashed_records()
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    $ownedTrashedReports = Report::factory()->count(2)->trashed()->create([
      'user_id' => $user->id,
    ]);

    $unownedTrashedReports = Report::factory()->count(2)->trashed()->create();
    $nonTrashedReports = Report::factory()->count(2)->create();

    $response = $this->get(route('reports.index'));
    $response->assertStatus(200);

    foreach ($ownedTrashedReports as $report) {
      $response->assertSee($report->animal_name);
    }

    foreach ($unownedTrashedReports as $report) {
      $response->assertDontSee($report->animal_name);
    }

    foreach ($nonTrashedReports as $report) {
      $response->assertSee($report->animal_name);
    }
  }

  public function test_admin_user_can_view_all_reports_including_trashed_ones()
  {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $nonTrashed = Report::factory()->count(3)->create();
    $trashed = Report::factory()->count(2)->trashed()->create();

    $response = $this->get(route('reports.index'));
    $response->assertStatus(200);

    foreach ($nonTrashed as $report) {
      $response->assertSee($report->animal_name);
    }

    foreach ($trashed as $report) {
      $response->assertSee($report->animal_name);
    }
  }

  public function test_staff_user_can_view_all_reports_including_trashed_ones()
  {
    $staff = User::factory()->staff()->create();
    $this->actingAs($staff);

    $nonTrashed = Report::factory()->count(3)->create();
    $trashed = Report::factory()->count(2)->trashed()->create();

    $response = $this->get(route('reports.index'));
    $response->assertStatus(200);

    foreach ($nonTrashed as $report) {
      $response->assertSee($report->animal_name);
    }

    foreach ($trashed as $report) {
      $response->assertSee($report->animal_name);
    }
  }

  public function test_searching_report_using_animal_name_returns_results_case_insensitive()
  {
    $matchingReport1 = Report::factory()->create([
      'animal_name' => 'Buddy',
    ]);

    $matchingReport2 = Report::factory()->create([
      'animal_name' => 'buddy',
    ]);

    $nonMatchingReport = Report::factory()->create([
      'animal_name' => 'Max',
    ]);

    // Act: Perform a GET request with a lowercase search term
    $response = $this->get(route('reports.index', ['search' => 'buddy']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching rescues are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('Reports/Index')
        // Check that the rescues prop exists and is paginated
        ->has('reports.data')
        // Check that the correct rescues are shown
        ->where('reports.data.0.animal_name', $matchingReport1->animal_name)
        ->where('reports.data.1.animal_name', $matchingReport2->animal_name)
        // Ensure non-matching rescue is not present
      ->missing('reports.data.2.animal_name',)
    );
  }

  public function test_searching_report_using_species_returns_results_case_insensitive()
  {
    $matchingReport1 = Report::factory()->create([
      'species' => 'Dog',
    ]);

    $matchingReport2 = Report::factory()->create([
      'species' => 'dog',
    ]);

    $nonMatchingReport = Report::factory()->create([
      'species' => 'Cat',
    ]);

    // Act: Perform a GET request with a lowercase search term
    $response = $this->get(route('reports.index', ['search' => 'dog']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching rescues are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('Reports/Index')
        // Check that the rescues prop exists and is paginated
        ->has('reports.data')
        // Check that the correct rescues are shown
        ->where('reports.data.0.species', $matchingReport1->species)
        ->where('reports.data.1.species', $matchingReport2->species)
        // Ensure non-matching rescue is not present
      ->missing('reports.data.2.species',)
    );
  }

  public function test_searching_report_using_sex_returns_results_case_insensitive()
  {
    $matchingReport1 = Report::factory()->create([
      'sex' => 'female',
    ]);

    $matchingReport2 = Report::factory()->create([
      'sex' => 'female',
    ]);

    $nonMatchingReport = Report::factory()->create([
      'sex' => 'male',
    ]);

    // Act: Perform a GET request with a uppercase search term
    $response = $this->get(route('reports.index', ['search' => 'FEMALE']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching rescues are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('Reports/Index')
        // Check that the rescues prop exists and is paginated
        ->has('reports.data')
        // Check that the correct rescues are shown
        ->where('reports.data.0.sex', $matchingReport1->sex)
        ->where('reports.data.1.sex', $matchingReport2->sex)
        // Ensure non-matching rescue is not present
      ->missing('reports.data.2.sex',)
    );
  }

  public function test_searching_report_using_breed_returns_results_case_insensitive()
  {
    $matchingReport1 = Report::factory()->create([
      'breed' => 'Aspin',
    ]);

    $matchingReport2 = Report::factory()->create([
      'breed' => 'aspin',
    ]);

    $nonMatchingReport = Report::factory()->create([
      'breed' => 'pitbull',
    ]);

    // Act: Perform a GET request with a uppercase search term
    $response = $this->get(route('reports.index', ['search' => 'ASPIN']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching rescues are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('Reports/Index')
        // Check that the rescues prop exists and is paginated
        ->has('reports.data')
        // Check that the correct rescues are shown
        ->where('reports.data.0.breed', $matchingReport1->breed)
        ->where('reports.data.1.breed', $matchingReport2->breed)
        // Ensure non-matching rescue is not present
      ->missing('reports.data.2.breed',)
    );
  }

  public function test_searching_report_using_color_returns_results_case_insensitive()
  {
    $matchingReport1 = Report::factory()->create([
      'color' => 'Black',
    ]);

    $matchingReport2 = Report::factory()->create([
      'color' => 'black',
    ]);

    $nonMatchingReport = Report::factory()->create([
      'color' => 'white',
    ]);

    // Act: Perform a GET request with a uppercase search term
    $response = $this->get(route('reports.index', ['search' => 'BLACK']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching rescues are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('Reports/Index')
        // Check that the rescues prop exists and is paginated
        ->has('reports.data')
        // Check that the correct rescues are shown
        ->where('reports.data.0.color', $matchingReport1->color)
        ->where('reports.data.1.color', $matchingReport2->color)
        // Ensure non-matching rescue is not present
      ->missing('reports.data.2.color',)
    );
  }

  public function test_searching_report_using_type_returns_results_case_insensitive()
  {
    $matchingReport1 = Report::factory()->create([
      'type' => 'lost',
    ]);

    $matchingReport2 = Report::factory()->create([
      'type' => 'lost',
    ]);

    $nonMatchingReport = Report::factory()->create([
      'type' => 'found',
    ]);

    // Act: Perform a GET request with a uppercase search term
    $response = $this->get(route('reports.index', ['search' => 'LOST']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching rescues are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('Reports/Index')
        // Check that the rescues prop exists and is paginated
        ->has('reports.data')
        // Check that the correct rescues are shown
        ->where('reports.data.0.type', $matchingReport1->type)
        ->where('reports.data.1.type', $matchingReport2->type)
        // Ensure non-matching rescue is not present
      ->missing('reports.data.2.type',)
    );
  }

  public function test_search_with_no_matches_returns_empty_results()
  {
    // Arrange: create some rescues that won't match the search term
    Report::factory()->count(5)->create(['animal_name' => 'Buddy']);

    // Act: visit the index route with a filter that yields no matches
    $response = $this->get(route('reports.index', ['search' => 'NonexistentAnimal']));

    // Assert: the Inertia response includes an empty rescues list
    $response->assertInertia(fn ($page) => 
      $page->component('Reports/Index')
        ->has('reports.data', 0) // no matching reports
      ->where('filters.search', 'NonexistentAnimal')
    );
  }

  public function test_search_filter_with_partial_animal_name_match_returns_correct_results()
  {
    
    $matchingReport1 = Report::factory()->create(['animal_name' => 'Buddy']);
    $matchingReport2 = Report::factory()->create(['animal_name' => 'buddy']);
    $nonMatchingReport = Report::factory()->create(['animal_name' => 'meow']);

    $response = $this->get(route('reports.index', ['search' => 'bud']));

    $response->assertStatus(200);

    
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('Reports/Index')
        ->has('reports.data')
      ->where('reports.data', function ($reports) use ($matchingReport1, $matchingReport2, $nonMatchingReport) {
        $names = collect($reports)->pluck('animal_name');

        return $names->contains($matchingReport1->animal_name)
          && $names->contains($matchingReport2->animal_name)
        && !$names->contains($nonMatchingReport->animal_name);
      })
    );
  }

  public function test_search_filter_with_multiple_keywords_returns_correct_results()
  {
    
    $report1 = Report::factory()->create(['animal_name' => 'Buddy', 'species' => 'Dog', 'sex' => 'female']);
    $report2 = Report::factory()->create(['animal_name' => 'Max', 'species' => 'Dog', 'sex' => 'male']);
    $report3 = Report::factory()->create(['animal_name' => 'Whiskers', 'species' => 'Cat', 'sex' => 'male']);

    $response = $this->get(route('reports.index', ['search' => 'Dog Buddy Female']));

    $response->assertStatus(200);

    
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('Reports/Index')
        ->has('reports.data')
      ->where('reports.data', function ($reports) use ($report1, $report2, $report3) {
        $names = collect($reports)->pluck('animal_name');

        return $names->contains($report1->animal_name)
          && !$names->contains($report2->animal_name)
        && !$names->contains($report3->animal_name);
      })
    );
  }

  public function test_lost_type_filter_returns_only_lost_reports()
  {
    Report::factory()->count(5)->lost()->create();
    Report::factory()->count(6)->found()->create();

    $response = $this->get(route('reports.index', ['type' => 'lost']));

    $response->assertInertia(fn ($page) => 
      $page->component('Reports/Index')
        ->has('reports.data') 
      ->where('filters.type', 'lost')
      ->has('reports.data', fn (Assert $reports) =>
        $reports->each(fn (Assert $report) =>
          $report->where('type', 'lost')->etc()
        )
      )
    );
  }

  public function test_found_type_filter_returns_only_found_reports()
  {
    Report::factory()->count(5)->lost()->create();
    Report::factory()->count(6)->found()->create();

    $response = $this->get(route('reports.index', ['type' => 'found']));

    $response->assertInertia(fn ($page) => 
      $page->component('Reports/Index')
        ->has('reports.data') 
      ->where('filters.type', 'found')
      ->has('reports.data', fn (Assert $reports) =>
        $reports->each(fn (Assert $report) =>
          $report->where('type', 'found')->etc()
        )
      )
    );
  }

  public function test_active_status_filter_returns_active_reports()
  {
    Report::factory()->count(5)->lost()->create(['status' => 'active']);
    Report::factory()->count(6)->found()->create(['status' => 'active']);
     Report::factory()->count(5)->lost()->create(['status' => 'resolved']);
    Report::factory()->count(6)->found()->create(['status' => 'resolved']);

    $response = $this->get(route('reports.index', ['status' => 'active']));

    $response->assertInertia(fn ($page) => 
      $page->component('Reports/Index')
        ->has('reports.data') 
      ->where('filters.status', 'active')
      ->has('reports.data', fn (Assert $reports) =>
        $reports->each(fn (Assert $report) =>
          $report->where('status', 'active')->etc()
        )
      )
    );
  }

  public function test_resolved_status_filter_returns_resolved_reports()
  {
    Report::factory()->count(5)->lost()->create(['status' => 'active']);
    Report::factory()->count(6)->found()->create(['status' => 'active']);
    Report::factory()->count(5)->lost()->create(['status' => 'resolved']);
    Report::factory()->count(6)->found()->create(['status' => 'resolved']);

    $response = $this->get(route('reports.index', ['status' => 'resolved']));

    $response->assertInertia(fn ($page) => 
      $page->component('Reports/Index')
        ->has('reports.data') 
      ->where('filters.status', 'resolved')
      ->has('reports.data', fn (Assert $reports) =>
        $reports->each(fn (Assert $report) =>
          $report->where('status', 'resolved')->etc()
        )
      )
    );
  }

  public function test_oldest_sort_filter_sorts_reports_in_ascending_order()
  {
    // Arrange: create reports with different created_at timestamps
    $report1 = Report::factory()->create(['created_at' => now()->subDays(3)]);
    $report2 = Report::factory()->create(['created_at' => now()->subDays(2)]);
    $report3 = Report::factory()->create(['created_at' => now()->subDay()]);

    // Act: request with ?sort=asc
    $response = $this->get(route('reports.index', ['sort' => 'asc']));

    // Assert: response OK
    $response->assertStatus(200);

    // Assert: reports are sorted by created_at ascending (oldest → newest)
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('Reports/Index')
        ->has('reports.data')
        ->where('reports.data', function ($reports) use ($report1, $report2, $report3) {
          $ids = collect($reports)->pluck('id')->toArray();

          $expectedOrder = [
            $report1->id, // oldest
            $report2->id,
            $report3->id, // newest
          ];

          return $ids === $expectedOrder;
        })
    );
  }

  public function test_newest_sort_filter_sorts_reports_in_descending_order()
  {
    // Arrange: create reports with different created_at timestamps
    $report1 = Report::factory()->create(['created_at' => now()->subDays(3)]);
    $report2 = Report::factory()->create(['created_at' => now()->subDays(2)]);
    $report3 = Report::factory()->create(['created_at' => now()->subDay()]);

    // Act: request with ?sort=asc
    $response = $this->get(route('reports.index', ['sort' => 'desc']));

    // Assert: response OK
    $response->assertStatus(200);

    // Assert: reports are sorted by created_at ascending (oldest → newest)
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('Reports/Index')
        ->has('reports.data')
        ->where('reports.data', function ($reports) use ($report1, $report2, $report3) {
          $ids = collect($reports)->pluck('id')->toArray();

          $expectedOrder = [
            $report3->id, // newest
            $report2->id,
            $report1->id, // oldest
          ];

          return $ids === $expectedOrder;
        })
    );
  }

  public function test_multiple_filters_and_search_work_together()
  {
    // Test that multiple filters and a search works at the same time.

    Report::factory()->lost()->create([
      'animal_name' => 'Buddy',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'color' => 'Black',
      'status' => 'active'
    ]);

    // Non-matching rescues
    Report::factory()->count(2)->create(['species' => 'Dog', 'breed' => 'Pitbull']);
    Report::factory()->count(2)->create(['species' => 'Dog', 'breed' => 'Labrador']);
    Report::factory()->count(2)->create(['species' => 'Cat', 'breed' => 'Puspin']);

    // Act: apply filters and search query
    $response = $this->get(route('reports.index', [
      'type' => 'lost',
      'status' => 'active',
      'search' => 'Buddy black aspin dog',
    ]));

    // Assert: Inertia props match
    $response->assertInertia(fn ($page) =>
      $page->component('Reports/Index')
        ->where('filters.type', 'lost')
        ->where('filters.status', 'active')
        ->where('filters.search', 'Buddy black aspin dog')
        ->has('reports.data', 1) // only Buddy should match
        ->where('reports.data.0.animal_name', 'Buddy')
        ->where('reports.data.0.type', 'lost')
      ->where('reports.data.0.status', 'active')
    );
  }

  public function test_combining_filters_respect_visibility_rules()
  {
    // Test taht trashed records still hidden for non-admin/staff users

    // Arrange
    $visible = Report::factory()->lost()->count(3)->create([
      'status' => 'active',
    ]);

    $trashed = Report::factory()->lost()->trashed()->count(4)->create([
      'status' => 'active',
    ]);

    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();

    // Act & Assert for regular user
    $this->actingAs($user);
    $userResponse = $this->get(route('reports.index', [
      'type' => 'lost',
      'status' => 'active',
    ]));

    $userResponse->assertInertia(fn ($page) =>
      $page->component('Reports/Index')
       ->has('reports.data', 3) // only non-trashed
        ->where('filters.type', 'lost')
        ->where('filters.status', 'active')
      ->where('reports.data', function ($reports) {
        // Ensure none are soft deleted
        return collect($reports)->every(fn ($r) => $r['deleted_at'] === null);
      })
    );

    // Act & Assert for admin
    $this->actingAs($admin);
    $adminResponse = $this->get(route('reports.index', [
      'type' => 'lost',
      'status' => 'active',
      'with_trashed' => true,
    ]));

    $adminResponse->assertInertia(fn ($page) =>
      $page->component('Reports/Index')
        ->has('reports.data', 7) // visible + trashed
        ->where('filters.type', 'lost')
      ->where('filters.status', 'active')
    );
  }

  public function test_search_and_filter_results_are_paginated_with_9_items_page()
  {
    // Arrange: Create 15 rescues that match search + filter
    Report::factory()->lost()->count(15)->create([
      'animal_name' => 'Buddy',
      'status' => 'active'
    ]);

    // Create some that should NOT match (different sex or name)
    Report::factory()->lost()->count(5)->create(['status' => 'resolved', 'animal_name' => 'Mittens']);

    // Act: Perform GET with both search and filter
    $response = $this->get(route('reports.index', [
      'search' => 'buddy',
      'type' => 'lost',
      'status' => 'active'
    ]));

    // Assert: Response OK & paginated with 9 items only
    $response->assertStatus(200);

    $response->assertInertia(fn ($page) =>
      $page->component('Reports/Index')
        ->where('filters.search', 'buddy')
        ->where('filters.type', 'lost')
        ->where('filters.status','active')
        ->has('reports.data', 9) // 9 items per page
      ->has('reports.links') // pagination links should exist
    );
  }

  public function test_query_string_parameters_are_preserved_in_pagination_links()
  {
    // Arrange: Create more rescues than one page
    Report::factory()->count(20)->create([
      'animal_name' => 'Luna',
      'status' => 'active'
    ]);

    // Act: Apply search and filter
    $response = $this->get(route('reports.index', [
      'search' => 'luna',
      'status' => 'active',
      'page' => 2,
    ]));

    // Assert: Check that pagination links include preserved query params
    $response->assertInertia(fn ($page) =>
      $page->component('Reports/Index')
        ->where('filters.search', 'luna')
        ->where('filters.status', 'active')
        ->has('reports.links') // just ensure 'links' exists
      ->where('reports.links', function ($links) {
        // Convert to collection for easier iteration
        return collect($links)->every(function ($link) {
        // Skip "null" links (like "previous" or "next" on first/last page)
        if (empty($link['url'])) return true;

        return str_contains($link['url'], 'search=luna') &&
          str_contains($link['url'], 'status=active');
        });
      })
    );
  }

  public function test_response_include_user()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('reports.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Reports/Index')
        ->has('user')
      ->where('user.id', $user->id)
      ->etc()
    );
  }

  public function test_filters_are_returned_in_response()
  {
    $params = [
      'search' => 'buddy',
      'type' => 'lost',
      'sort' => 'asc',
      'status' => 'active',
    ];

    $response = $this->get(route('reports.index', $params));

    $response->assertInertia(fn ($page) =>
      $page->component('Reports/Index')
        ->where('filters.search', 'buddy')
        ->where('filters.type', 'lost')
        ->where('filters.sort', 'asc')
      ->where('filters.status', 'active')
    );
  }

  public function test_reports_are_ordered_by_created_at_in_descending_order_by_default()
  {
    // Create rescues out of order
    $report1 = Report::factory()->create(['created_at' => now()->subDays(3)]);
    $report2 = Report::factory()->create(['created_at' => now()->subDays(2)]);
    $report3 = Report::factory()->create(['created_at' => now()->subDay()]);

    // Act — hit the rescues index route
    $response = $this->get(route('reports.index'));

    // Assert response OK
    $response->assertStatus(200);

    // Extract rescues from Inertia props
    $props = $response->original->getData()['page']['props'];
    $reports = $props['reports']['data'] ?? [];

    // Assert there are 3 reports returned
    $this->assertCount(3, $reports);

    // Extract their IDs to compare order
    $ids = array_column($reports, 'id');

    // Expected order: newest first, oldest last
    $expectedOrder = [
      $report3->id, // newest
      $report2->id,
      $report1->id, // oldest
    ];

    $this->assertSame($expectedOrder, $ids, 'Reports should be ordered by created_at descending by default.');

  }

  public function test_empty_string_search_returns_all_available_reports()
  {
    $reports = Report::factory()->count(5)->create();
    
    $response = $this->get(route('reports.index', ['search' => '']));
    
    $response->assertInertia(fn ($page) =>
      $page->component('Reports/Index')
        ->where('filters.search', null)
       ->has('reports.data', 5)
    );
  }

  public function test_search_handles_special_characters_safely()
  {
    $report = Report::factory()->create(['animal_name' => "O'Malley"]);
    
    $response = $this->get(route('reports.index', ['search' => "O'Mal"]));
    
    $response->assertInertia(fn ($page) =>
      $page->component('Reports/Index')
        ->has('reports.data')
      ->where('reports.data.0.animal_name', "O'Malley")
    );
  }

  public function test_requesting_page_beyond_available_pages_returns_empty_results()
  {
    Report::factory()->count(5)->create();
    
    $response = $this->get(route('reports.index', ['page' => 999]));
    
    $response->assertInertia(fn ($page) =>
      $page->component('Reports/Index')->has('reports.data', 0)->etc()
    );
  }
  
  public function test_search_with_url_encoded_characters()
  {
    $report = Report::factory()->create(['animal_name' => 'Max & Ruby']);
    
    $response = $this->get(route('reports.index', ['search' => 'Max & Ruby']));
    
    $response->assertInertia(fn ($page) =>
      $page->component('Reports/Index')->has('reports.data', 1)
    );
  }

  public function test_response_includes_lostModal_value()
  {
    //Simulate guest user
    $response = $this->get(route('reports.index'));
    $response->assertStatus(200);
    $response->assertInertia(fn ($page) =>
      $page->component('Reports/Index')->where('lostModal', '#loginReminderModal')
    );

    //Simulate authenticated user
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('reports.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Reports/Index')
        ->has('user')
        ->where('user.id', $user->id)
        ->has('lostModal')
        ->where('lostModal', '#createLostAnimalReportModal')
      ->etc()
    );
  }

  public function test_response_includes_foundModal_value()
  {
    //Simulate guest user
    $response = $this->get(route('reports.index'));
    $response->assertStatus(200);
    $response->assertInertia(fn ($page) =>
      $page->component('Reports/Index')->where('foundModal', '#loginReminderModal')
    );

    //Simulate authenticated user
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('reports.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Reports/Index')
        ->has('user')
        ->where('user.id', $user->id)
        ->has('foundModal')
        ->where('foundModal', '#createFoundAnimalReportModal')
      ->etc()
    );
  }

  public function test_last_page_with_filters_shows_remaining_items()
  {
    Report::factory()->lost()->count(11)->create(['status' => 'active']);
    
    $response = $this->get(route('reports.index', [
      'type' => 'lost',
      'status' => 'active',
      'page' => 2
    ]));
    
    $response->assertInertia(fn ($page) =>
      $page->component('Reports/Index')->has('reports.data', 2) // 11 items, 9 per page = 2 on page 2
    );
  }

  public function test_extremely_long_search_string_handles_gracefully()
  {
    Report::factory()->create(['animal_name' => 'Buddy']);
    
    $longString = str_repeat('a', 1000);
    
    $response = $this->get(route('reports.index', ['search' => $longString]));
    
    $response->assertStatus(200);
  }

  public function test_search_with_sql_injection_attempt_is_handled_safely()
  {
    Report::factory()->create(['animal_name' => 'Buddy']);
    
    $response = $this->get(route('reports.index', [
        'search' => "'; DROP TABLE reports; --"
    ]));
    
    $response->assertStatus(200);
    $response->assertInertia(fn ($page) =>
        $page->component('Reports/Index')->has('reports.data', 0)
    );
    
    // Verify the table still exists
    $this->assertDatabaseHas('reports', ['animal_name' => 'Buddy']);
  }

  public function test_type_filter_is_case_sensitive()
  {
    Report::factory()->lost()->count(3)->create();
    
    // 'LOST' in uppercase shouldn't match 'lost' in database
    $response = $this->get(route('reports.index', ['type' => 'LOST']));
    
    $response->assertInertia(fn ($page) =>
      $page->component('Reports/Index')
        ->where('filters.type', 'LOST')
      ->has('reports.data', 0) // Should return no results
    );
  }

  public function test_empty_search_with_other_filters_works_correctly()
  {
    Report::factory()->lost()->count(3)->create();
    Report::factory()->found()->count(2)->create();
    
    $response = $this->get(route('reports.index', [
      'search' => '',
      'type' => 'lost'
    ]));
    
    $response->assertInertia(fn ($page) =>
      $page->component('Reports/Index')
        ->where('filters.search', null)
        ->where('filters.type', 'lost')
      ->has('reports.data', 3)
    );
  }

  public function test_invalid_sort_parameter_defaults_to_desc_order()
  {
    $report1 = Report::factory()->create(['created_at' => now()->subDays(2)]);
    $report2 = Report::factory()->create(['created_at' => now()->subDay()]);
    
    // Invalid sort value should be ignored, defaulting to desc
    $response = $this->get(route('reports.index', ['sort' => 'invalid']));
    
    $response->assertInertia(fn ($page) =>
      $page->component('Reports/Index')
        ->where('filters.sort', null) // Should be null for invalid values
      ->where('reports.data.0.id', $report2->id) // Newest first (desc)
    );
  }
}
