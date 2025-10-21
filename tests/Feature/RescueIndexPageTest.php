<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Rescue;
use App\Models\User;
use App\Models\AdoptionApplication;
use App\Models\Household;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;


class RescueIndexPageTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_can_view_index_page()
  {
    $response = $this->get(route('rescues.index'));
    $response->assertStatus(200);

    $response->assertViewIs('app');
  }

  public function test_authenticated_regular_user_can_view_index_page()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('rescues.index'));
    $response->assertStatus(200);

    $response->assertViewIs('app');
  }

  public function test_authenticated_admin_user_can_view_index_page()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $response = $this->get(route('rescues.index'));
    $response->assertStatus(200);

    $response->assertViewIs('app');
  }

  public function test_authenticated_staff_user_can_view_index_page()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $response = $this->get(route('rescues.index'));
    $response->assertStatus(200);

    $response->assertViewIs('app');
  }

  public function test_guest_user_can_only_view_non_trashed_records()
  {
    $nonTrashed = Rescue::factory()->count(3)->create(); 
    $trashed = Rescue::factory()->trashed()->count(3)->create(); 

    $response = $this->get(route('rescues.index'));
    $response->assertStatus(200);

    foreach ($nonTrashed as $rescue) {
      $response->assertSee($rescue->name_formatted);
    }

    foreach ($trashed as $rescue) {
      $response->assertDontSee($rescue->name_formatted);
    }
  }

  public function test_regular_user_can_only_view_non_trashed_records()
  {
    $nonTrashed = Rescue::factory()->count(3)->create();
    $trashed = Rescue::factory()->trashed()->count(3)->create();

    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('rescues.index'));
    $response->assertStatus(200);

    foreach ($nonTrashed as $rescue) {
      $response->assertSee($rescue->name_formatted);
    }

    foreach ($trashed as $rescue) {
      $response->assertDontSee($rescue->name_formatted);
    }
  }

  public function test_admin_user_can_view_all_records()
  {
    $nonTrashed = Rescue::factory()->count(3)->create();
    $trashed = Rescue::factory()->trashed()->count(3)->create();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    
    $response = $this->get(route('rescues.index'));
    $response->assertStatus(200);

    foreach ($nonTrashed as $rescue) {
      $response->assertSee($rescue->name_formatted);
    }

    foreach ($trashed as $rescue) {
      $response->assertSee($rescue->name_formatted);
    }
  }

  public function test_staff_user_can_view_all_records()
  {
    $nonTrashed = Rescue::factory()->count(3)->create();
    $trashed = Rescue::factory()->trashed()->count(3)->create();

    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);
    
    $response = $this->get(route('rescues.index'));
    $response->assertStatus(200);

    foreach ($nonTrashed as $rescue) {
      $response->assertSee($rescue->name_formatted);
    }

    foreach ($trashed as $rescue) {
      $response->assertSee($rescue->name_formatted);
    }
  }

  public function test_search_filter_returns_rescues_matching_the_search_term_case_insensitive()
  {
    // Arrange: Create rescues with specific names
    $matchingRescue1 = Rescue::factory()->create(['name' => 'Buddy']);
    $matchingRescue2 = Rescue::factory()->create(['name' => 'buddy']);
    $nonMatchingRescue = Rescue::factory()->create(['name' => 'meow']);

    // Act: Perform a GET request with a lowercase search term
    $response = $this->get(route('rescues.index', ['search' => 'buddy']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching rescues are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('Rescues/Index')
        // Check that the rescues prop exists and is paginated
        ->has('rescues.data')
        // Check that the correct rescues are shown
        ->where('rescues.data.0.name', $matchingRescue1->name)
        ->where('rescues.data.1.name', $matchingRescue2->name)
        // Ensure non-matching rescue is not present
      ->missing('rescues.data.2.name',)
    );

  }

  public function test_search_filter_with_partial_name_match_returns_correct_results()
  {
    // Arrange: Create rescues with specific names
    $matchingRescue1 = Rescue::factory()->create(['name' => 'Buddy']);
    $matchingRescue2 = Rescue::factory()->create(['name' => 'buddy']);
    $nonMatchingRescue = Rescue::factory()->create(['name' => 'meow']);

    // Act: Perform a GET request with a lowercase search term
    $response = $this->get(route('rescues.index', ['search' => 'bud']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching rescues are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('Rescues/Index')
        // Check that the rescues prop exists and is paginated
        ->has('rescues.data')
        // Check that the correct rescues are shown
      ->where('rescues.data', function ($rescues) use ($matchingRescue1, $matchingRescue2, $nonMatchingRescue) {
        $names = collect($rescues)->pluck('name');

        // Assert matching rescues are found
        return $names->contains($matchingRescue1->name)
          && $names->contains($matchingRescue2->name)
          // Assert non-matching one is NOT found
        && !$names->contains($nonMatchingRescue->name);
      })
    );
  }

  public function test_search_with_no_matches_returns_empty_results()
  {
    // Arrange: create some rescues that won't match the search term
    Rescue::factory()->count(5)->create(['name' => 'Buddy']);

    // Act: visit the index route with a filter that yields no matches
    $response = $this->get(route('rescues.index', ['search' => 'NonexistentAnimal']));

    // Assert: the Inertia response includes an empty rescues list
    $response->assertInertia(fn ($page) => 
      $page->component('Rescues/Index')
        ->has('rescues.data', 0) // no matching rescues
      ->where('filters.search', 'NonexistentAnimal')
    );
  }

  public function test_male_sex_filter_returns_only_male_rescues()
  {
    Rescue::factory()->count(5)->create(['sex' => 'male']);
    Rescue::factory()->count(6)->create(['sex' => 'female']);

    $response = $this->get(route('rescues.index', ['sex' => 'male']));

    $response->assertInertia(fn ($page) => 
      $page->component('Rescues/Index')
        ->has('rescues.data') 
      ->where('filters.sex', 'male')
      ->has('rescues.data', fn (Assert $rescues) =>
        $rescues->each(fn (Assert $rescue) =>
          $rescue->where('sex', 'male')->etc()
        )
      )
    );
  }

  public function test_female_sex_filter_returns_only_female_rescues()
  {
    Rescue::factory()->count(5)->create(['sex' => 'male']);
    Rescue::factory()->count(6)->create(['sex' => 'female']);

    $response = $this->get(route('rescues.index', ['sex' => 'female']));

    $response->assertInertia(fn ($page) => 
      $page->component('Rescues/Index')
        ->has('rescues.data') 
      ->where('filters.sex', 'female')
      ->has('rescues.data', fn (Assert $rescues) =>
        $rescues->each(fn (Assert $rescue) =>
          $rescue->where('sex', 'female')->etc()
        )
      )
    );
  }

  public function test_small_size_filter_returns_only_small_rescues()
  {
    Rescue::factory()->count(5)->create(['size' => 'small']);
    Rescue::factory()->count(6)->create(['size' => 'medium']);
    Rescue::factory()->count(7)->create(['size' => 'large']);

    $response = $this->get(route('rescues.index', ['size' => 'small']));

    $response->assertInertia(fn ($page) => 
      $page->component('Rescues/Index')
        ->has('rescues.data') 
      ->where('filters.size', 'small')
      ->has('rescues.data', fn (Assert $rescues) =>
        $rescues->each(fn (Assert $rescue) =>
          $rescue->where('size', 'small')->etc()
        )
      )
    );
  }

  public function test_medium_size_filter_returns_only_medium_rescues()
  {
    Rescue::factory()->count(5)->create(['size' => 'small']);
    Rescue::factory()->count(6)->create(['size' => 'medium']);
    Rescue::factory()->count(7)->create(['size' => 'large']);

    $response = $this->get(route('rescues.index', ['size' => 'medium']));

    $response->assertInertia(fn ($page) => 
      $page->component('Rescues/Index')
        ->has('rescues.data') 
      ->where('filters.size', 'medium')
      ->has('rescues.data', fn (Assert $rescues) =>
        $rescues->each(fn (Assert $rescue) =>
          $rescue->where('size', 'medium')->etc()
        )
      )
    );
  }

  public function test_large_size_filter_returns_only_large_rescues()
  {
    Rescue::factory()->count(5)->create(['size' => 'small']);
    Rescue::factory()->count(6)->create(['size' => 'medium']);
    Rescue::factory()->count(7)->create(['size' => 'large']);

    $response = $this->get(route('rescues.index', ['size' => 'large']));

    $response->assertInertia(fn ($page) => 
      $page->component('Rescues/Index')
        ->has('rescues.data') 
      ->where('filters.size', 'large')
      ->has('rescues.data', fn (Assert $rescues) =>
        $rescues->each(fn (Assert $rescue) =>
          $rescue->where('size', 'large')->etc()
        )
      )
    );
  }

  public function test_available_status_filter_returns_available_rescues()
  {
    Rescue::factory()->count(5)->create();
    Rescue::factory()->count(6)->available()->create();
    Rescue::factory()->count(7)->unavailable()->create();

    $response = $this->get(route('rescues.index', ['status' => 'available']));

    $response->assertInertia(fn ($page) => 
      $page->component('Rescues/Index')
        ->has('rescues.data') 
      ->where('filters.status', 'available')
      ->has('rescues.data', fn (Assert $rescues) =>
        $rescues->each(fn (Assert $rescue) =>
          $rescue->where('adoption_status', 'available')->etc()
        )
      )
    );
  }

  public function test_unavailable_status_filter_returns_unavailable_rescues()
  {
    Rescue::factory()->count(5)->create();
    Rescue::factory()->count(6)->available()->create();
    Rescue::factory()->count(7)->unavailable()->create();

    $response = $this->get(route('rescues.index', ['status' => 'unavailable']));

    $response->assertInertia(fn ($page) => 
      $page->component('Rescues/Index')
        ->has('rescues.data',7) 
      ->where('filters.status', 'unavailable')
      ->has('rescues.data', fn (Assert $rescues) =>
        $rescues->each(fn (Assert $rescue) =>
          $rescue->where('adoption_status', 'unavailable')->etc()
        )
      )
    );
  }

  public function test_adopted_status_filter_returns_adopted_rescues()
  {
    Rescue::factory()->count(5)->create();
    Rescue::factory()->count(6)->available()->create();
    Rescue::factory()->count(7)->unavailable()->create();

    $response = $this->get(route('rescues.index', ['status' => 'adopted']));

    $response->assertInertia(fn ($page) => 
      $page->component('Rescues/Index')
        ->has('rescues.data',5) 
      ->where('filters.status', 'adopted')
      ->has('rescues.data', fn (Assert $rescues) =>
        $rescues->each(fn (Assert $rescue) =>
          $rescue->where('adoption_status', 'adopted')->etc()
        )
      )
    );
  }

  public function test_multiple_filters_and_search_work_together()
  {
    // Test that multiple filters and a search works at the same time.

    // Arrange: create rescues that match and don't match the filters
    Rescue::factory()->create([
      'name' => 'Buddy',
      'sex' => 'male',
      'size' => 'small',
      'adoption_status' => 'available',
    ]);

    // Non-matching rescues
    Rescue::factory()->count(2)->create(['sex' => 'female', 'size' => 'small', 'adoption_status' => 'available']);
    Rescue::factory()->count(2)->create(['sex' => 'male', 'size' => 'large', 'adoption_status' => 'available']);
    Rescue::factory()->count(2)->create(['sex' => 'male', 'size' => 'small', 'adoption_status' => 'adopted']);

    // Act: apply filters and search query
    $response = $this->get(route('rescues.index', [
      'sex' => 'male',
      'size' => 'small',
      'status' => 'available',
      'search' => 'Buddy',
    ]));

    // Assert: Inertia props match
    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Index')
        ->where('filters.sex', 'male')
        ->where('filters.size', 'small')
        ->where('filters.status', 'available')
        ->where('filters.search', 'Buddy')
        ->has('rescues.data', 1) // only Buddy should match
        ->where('rescues.data.0.name', 'Buddy')
        ->where('rescues.data.0.sex', 'male')
        ->where('rescues.data.0.size', 'small')
      ->where('rescues.data.0.adoption_status', 'available')
    );
  }

  public function test_combining_filters_respect_visibility_rules()
  {
    // Test taht trashed records still hidden for non-admin/staff users

    // Arrange
    $visible = Rescue::factory()->count(3)->create([
      'sex' => 'male',
      'size' => 'medium',
      'adoption_status' => 'available',
    ]);

    $trashed = Rescue::factory()->trashed()->count(4)->create([
      'sex' => 'male',
      'size' => 'medium',
      'adoption_status' => 'available',
    ]);

    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();

    // Act & Assert for regular user
    $this->actingAs($user);
    $userResponse = $this->get(route('rescues.index', [
      'sex' => 'male',
      'size' => 'medium',
      'status' => 'available',
    ]));

    $userResponse->assertInertia(fn ($page) =>
      $page->component('Rescues/Index')
       ->has('rescues.data', 3) // only non-trashed
        ->where('filters.sex', 'male')
        ->where('filters.size', 'medium')
        ->where('filters.status', 'available')
      ->where('rescues.data', function ($rescues) {
        // Ensure none are soft deleted
        return collect($rescues)->every(fn ($r) => $r['deleted_at'] === null);
      })
    );

    // Act & Assert for admin
    $this->actingAs($admin);
    $adminResponse = $this->get(route('rescues.index', [
      'sex' => 'male',
      'size' => 'medium',
      'status' => 'available',
      'with_trashed' => true,
    ]));

    $adminResponse->assertInertia(fn ($page) =>
      $page->component('Rescues/Index')
        ->has('rescues.data', 7) // visible + trashed
        ->where('filters.sex', 'male')
        ->where('filters.size', 'medium')
      ->where('filters.status', 'available')
    );
  }

  public function test_search_and_filter_results_are_paginated_with_9_items_page()
  {
    // Arrange: Create 15 rescues that match search + filter
    Rescue::factory()->count(15)->create([
      'sex' => 'male',
      'name' => 'Buddy',
    ]);

    // Create some that should NOT match (different sex or name)
    Rescue::factory()->count(5)->create(['sex' => 'female', 'name' => 'Mittens']);

    // Act: Perform GET with both search and filter
    $response = $this->get(route('rescues.index', [
      'search' => 'buddy',
      'sex' => 'male',
    ]));

    // Assert: Response OK & paginated with 9 items only
    $response->assertStatus(200);

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Index')
        ->where('filters.search', 'buddy')
        ->where('filters.sex', 'male')
        ->has('rescues.data', 9) // 9 items per page
      ->has('rescues.links') // pagination links should exist
    );
  }

  public function test_query_string_parameters_are_preserved_in_pagination_links()
  {
    // Arrange: Create more rescues than one page
    Rescue::factory()->count(20)->create([
      'sex' => 'female',
      'name' => 'Luna',
    ]);

    // Act: Apply search and filter
    $response = $this->get(route('rescues.index', [
      'search' => 'luna',
      'sex' => 'female',
      'page' => 2,
    ]));

    // Assert: Check that pagination links include preserved query params
    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Index')
        ->where('filters.search', 'luna')
        ->where('filters.sex', 'female')
        ->has('rescues.links') // just ensure 'links' exists
      ->where('rescues.links', function ($links) {
        // Convert to collection for easier iteration
        return collect($links)->every(function ($link) {
        // Skip "null" links (like "previous" or "next" on first/last page)
        if (empty($link['url'])) return true;

        return str_contains($link['url'], 'search=luna') &&
          str_contains($link['url'], 'sex=female');
        });
      })
    );
  }

  public function test_response_includes_rescues_with_adoption_applications_count()
  {
    $rescueWithApps = Rescue::factory()->create();

    AdoptionApplication::factory()->count(3)->create([
      'rescue_id' => $rescueWithApps->id,
    ]);
    $response = $this->get(route('rescues.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Index')
      ->has('rescues.data')
      ->where('rescues.data', function ($rescues) use ($rescueWithApps) {
        return collect($rescues)
          ->contains(fn ($rescue) =>
          ($rescue['id'] ?? null) === $rescueWithApps->id &&
          ($rescue['adoption_applications_count'] ?? null) === 3
        );
      })
    );
  }

  public function test_user_data_includes_isAdminOrStaff_for_authenticated_user()
  {
    //Simulate regular user logged in
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('rescues.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Index')
        ->has('user')
        ->where('user.id', $user->id)
      ->where('user.isAdminOrStaff', false)
    );

    // Simulate admin user logged in
    $user = User::factory()->admin()->create();

    $this->actingAs($user);

    $response = $this->get(route('rescues.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Index')
        ->has('user')
        ->where('user.id', $user->id)
      ->where('user.isAdminOrStaff', true)
    );

    //Simulate staff
    $user = User::factory()->staff()->create();

    $this->actingAs($user);

    $response = $this->get(route('rescues.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Index')
        ->has('user')
        ->where('user.id', $user->id)
      ->where('user.isAdminOrStaff', true)
    );
  }
  public function test_user_data_includes_canAdopt_result_for_authenticated_user()
  {
    $user = User::factory()->create();

    $address = Address::factory()->create([
      'user_id' => $user->id,
    ]);

    $household = Household::factory()->create([
      'user_id' => $user->id,
    ]);


    $this->actingAs($user);

    $response = $this->get(route('rescues.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Index')
        ->has('user')
        ->where('user.id', $user->id)
      ->where('user.canAdopt', true)
    );
  }

  public function test_user_cannot_adopt_if_missing_address_or_household()
  {
    $user = User::factory()->create();
    // no address or household created

    $this->actingAs($user);

    $response = $this->get(route('rescues.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Index')
        ->has('user')
        ->where('user.id', $user->id)
      ->where('user.canAdopt', false)
    );
  }

  public function test_user_data_includes_address_relationships()
  {
    $user = User::factory()->create();

    $address = Address::factory()->create([
      'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->get(route('rescues.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Index')
        ->where('user.id', $user->id)
      ->has('user.address')
    );
  }

  public function test_user_data_includes_household_relationships()
  {
    $user = User::factory()->create();

    $household = Household::factory()->create([
      'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->get(route('rescues.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Index')
        ->where('user.id', $user->id)
      ->has('user.household')
    );
  }

  public function test_filters_are_returned_in_response()
  {
    $params = [
      'search' => 'buddy',
      'sex' => 'male',
      'size' => 'small',
      'status' => 'available',
    ];

    $response = $this->get(route('rescues.index', $params));

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Index')
        ->where('filters.search', 'buddy')
        ->where('filters.sex', 'male')
        ->where('filters.size', 'small')
      ->where('filters.status', 'available')
    );
  }

  public function test_user_is_null_in_response_for_guest_users()
  {
    $response = $this->get(route('rescues.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Index')->where('user', null)
    );
  }

  public function test_rescues_are_ordered_by_id_in_ascending_order_by_default()
  {
    // Create rescues out of order
    $rescue3 = Rescue::factory()->create(['id' => 3]);
    $rescue1 = Rescue::factory()->create(['id' => 1]);
    $rescue2 = Rescue::factory()->create(['id' => 2]);

    // Act — hit the rescues index route
    $response = $this->get(route('rescues.index'));

    // Assert response OK
    $response->assertStatus(200);

    // Extract rescues from Inertia props
    $props = $response->original->getData()['page']['props'];
    $rescues = $props['rescues']['data'] ?? [];

    // Get IDs in the order they were returned
    $ids = array_column($rescues, 'id');

    // Assert that IDs are in ascending order
    $sorted = $ids;
    sort($sorted);
    $this->assertEquals($sorted, $ids, 'Rescues should be ordered by ID in ascending order by default.');
  }
}
