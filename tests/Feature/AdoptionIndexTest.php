<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Rescue;
use Inertia\Testing\AssertableInertia as Assert;
use App\Models\AdoptionApplication;
use App\Models\Address;
use App\Models\Household;
class AdoptionIndexTest extends TestCase
{
  use RefreshDatabase;

  public function test_adoption_index_page_only_show_rescue_record_with_available_adoption_status()
  {
    $availableRescues = Rescue::factory()->available()->count(3)->create();
    $adoptedRescues = Rescue::factory()->count(2)->create();
    $unavailableRescues = Rescue::factory()->unavailable()->count(2)->create();

    $response = $this->get(route('adoption.index'));
    $response->assertStatus(200);

    foreach ($availableRescues as $rescue) {
      $response->assertSee($rescue->name_formatted);
    }

    foreach ($adoptedRescues as $rescue) {
      $response->assertDontSee($rescue->name_formatted);
    }

    foreach ($unavailableRescues as $rescue) {
      $response->assertDontSee($rescue->name_formatted);
    }
  }
  public function test_guest_user_can_access_adoption_index()
  {
    $response = $this->get(route('adoption.index'));
    $response->assertStatus(200);

    $response->assertViewIs('app');
  }

  public function test_authenticated_regular_user_can_access_adoption_index()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('adoption.index'));
    $response->assertStatus(200);

    $response->assertViewIs('app');
  }

  public function test_authenticated_admin_user_can_access_adoption_index()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $response = $this->get(route('adoption.index'));
    $response->assertStatus(200);

    $response->assertViewIs('app');
  }

  public function test_authenticated_staff_user_can_access_adoption_index()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $response = $this->get(route('adoption.index'));
    $response->assertStatus(200);

    $response->assertViewIs('app');
  }

  public function test_guest_user_can_only_view_non_trashed_adoptable_records()
  {
    $nonTrashed = Rescue::factory()->available()->count(3)->create(); 
    $trashed = Rescue::factory()->available()->trashed()->count(3)->create(); 

    $response = $this->get(route('adoption.index'));
    $response->assertStatus(200);

    foreach ($nonTrashed as $rescue) {
      $response->assertSee($rescue->name_formatted);
    }

    foreach ($trashed as $rescue) {
      $response->assertDontSee($rescue->name_formatted);
    }
  }

  public function test_regular_user_can_only_view_non_trashed_adoptable_records()
  {
    $nonTrashed = Rescue::factory()->available()->count(3)->create();
    $trashed = Rescue::factory()->available()->trashed()->count(3)->create();

    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('adoption.index'));
    $response->assertStatus(200);

    foreach ($nonTrashed as $rescue) {
      $response->assertSee($rescue->name_formatted);
    }

    foreach ($trashed as $rescue) {
      $response->assertDontSee($rescue->name_formatted);
    }
  }

  public function test_admin_user_can_view_all_adoptable_records()
  {
    $nonTrashed = Rescue::factory()->available()->count(3)->create();
    $trashed = Rescue::factory()->available()->trashed()->count(3)->create();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    
    $response = $this->get(route('adoption.index'));
    $response->assertStatus(200);

    foreach ($nonTrashed as $rescue) {
      $response->assertSee($rescue->name_formatted);
    }

    foreach ($trashed as $rescue) {
      $response->assertSee($rescue->name_formatted);
    }
  }

  public function test_staff_user_can_view_all_adoptable_records()
  {
    $nonTrashed = Rescue::factory()->available()->count(3)->create();
    $trashed = Rescue::factory()->available()->trashed()->count(3)->create();

    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);
    
    $response = $this->get(route('adoption.index'));
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
    $matchingRescue1 = Rescue::factory()->available()->create(['name' => 'Buddy']);
    $matchingRescue2 = Rescue::factory()->available()->create(['name' => 'buddy']);
    $nonMatchingRescue = Rescue::factory()->available()->create(['name' => 'meow']);

    // Act: Perform a GET request with a lowercase search term
    $response = $this->get(route('adoption.index', ['search' => 'buddy']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching rescues are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('Adoption/Index')
        // Check that the rescues prop exists and is paginated
        ->has('adoptables.data')
        // Check that the correct rescues are shown
      ->where('adoptables.data', function ($rescues) use ($matchingRescue1, $matchingRescue2, $nonMatchingRescue) {
        $names = collect($rescues)->pluck('name');

        // Assert matching rescues are found
        return $names->contains($matchingRescue1->name)
          && $names->contains($matchingRescue2->name)
          // Assert non-matching one is NOT found
        && !$names->contains($nonMatchingRescue->name);
      })
    );

  }

  public function test_search_filter_with_partial_name_match_returns_correct_results()
  {
    // Arrange: Create rescues with specific names
    $matchingRescue1 = Rescue::factory()->available()->create(['name' => 'Buddy']);
    $matchingRescue2 = Rescue::factory()->available()->create(['name' => 'buddy']);
    $nonMatchingRescue = Rescue::factory()->available()->create(['name' => 'meow']);

    // Act: Perform a GET request with a lowercase search term
    $response = $this->get(route('adoption.index', ['search' => 'bud']));

    // Assert: Response OK
    $response->assertStatus(200);

    // Assert that the matching rescues are present and non-matching are not
    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('Adoption/Index')
        // Check that the rescues prop exists and is paginated
        ->has('adoptables.data')
        // Check that the correct rescues are shown
      ->where('adoptables.data', function ($rescues) use ($matchingRescue1, $matchingRescue2, $nonMatchingRescue) {
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
    Rescue::factory()->available()->count(5)->create(['name' => 'Buddy']);

    // Act: visit the index route with a filter that yields no matches
    $response = $this->get(route('adoption.index', ['search' => 'NonexistentAnimal']));

    // Assert: the Inertia response includes an empty rescues list
    $response->assertInertia(fn ($page) => 
      $page->component('Adoption/Index')
        ->has('adoptables.data', 0) // no matching rescues
      ->where('filters.search', 'NonexistentAnimal')
    );
  }

  public function test_male_sex_filter_returns_only_male_rescues()
  {
    Rescue::factory()->available()->count(5)->create(['sex' => 'male']);
    Rescue::factory()->available()->count(6)->create(['sex' => 'female']);

    $response = $this->get(route('adoption.index', ['sex' => 'male']));

    $response->assertInertia(fn ($page) => 
      $page->component('Adoption/Index')
        ->has('adoptables.data') 
      ->where('filters.sex', 'male')
      ->has('adoptables.data', fn (Assert $rescues) =>
        $rescues->each(fn (Assert $rescue) =>
          $rescue->where('sex', 'male')->etc()
        )
      )
    );
  }

  public function test_female_sex_filter_returns_only_female_rescues()
  {
    Rescue::factory()->available()->count(5)->create(['sex' => 'male']);
    Rescue::factory()->available()->count(6)->create(['sex' => 'female']);

    $response = $this->get(route('adoption.index', ['sex' => 'female']));

    $response->assertInertia(fn ($page) => 
      $page->component('Adoption/Index')
        ->has('adoptables.data') 
      ->where('filters.sex', 'female')
      ->has('adoptables.data', fn (Assert $rescues) =>
        $rescues->each(fn (Assert $rescue) =>
          $rescue->where('sex', 'female')->etc()
        )
      )
    );
  }

  public function test_small_size_filter_returns_only_small_rescues()
  {
    Rescue::factory()->available()->count(5)->create(['size' => 'small']);
    Rescue::factory()->available()->count(6)->create(['size' => 'medium']);
    Rescue::factory()->available()->count(7)->create(['size' => 'large']);

    $response = $this->get(route('adoption.index', ['size' => 'small']));

    $response->assertInertia(fn ($page) => 
      $page->component('Adoption/Index')
        ->has('adoptables.data') 
      ->where('filters.size', 'small')
      ->has('adoptables.data', fn (Assert $rescues) =>
        $rescues->each(fn (Assert $rescue) =>
          $rescue->where('size', 'small')->etc()
        )
      )
    );
  }

  public function test_medium_size_filter_returns_only_medium_rescues()
  {
    Rescue::factory()->available()->count(5)->create(['size' => 'small']);
    Rescue::factory()->available()->count(6)->create(['size' => 'medium']);
    Rescue::factory()->available()->count(7)->create(['size' => 'large']);

    $response = $this->get(route('adoption.index', ['size' => 'medium']));

    $response->assertInertia(fn ($page) => 
      $page->component('Adoption/Index')
        ->has('adoptables.data') 
      ->where('filters.size', 'medium')
      ->has('adoptables.data', fn (Assert $rescues) =>
        $rescues->each(fn (Assert $rescue) =>
          $rescue->where('size', 'medium')->etc()
        )
      )
    );
  }

  public function test_large_size_filter_returns_only_large_rescues()
  {
    Rescue::factory()->available()->count(5)->create(['size' => 'small']);
    Rescue::factory()->available()->count(6)->create(['size' => 'medium']);
    Rescue::factory()->available()->count(7)->create(['size' => 'large']);

    $response = $this->get(route('adoption.index', ['size' => 'large']));

    $response->assertInertia(fn ($page) => 
      $page->component('Adoption/Index')
        ->has('adoptables.data') 
      ->where('filters.size', 'large')
      ->has('adoptables.data', fn (Assert $rescues) =>
        $rescues->each(fn (Assert $rescue) =>
          $rescue->where('size', 'large')->etc()
        )
      )
    );
  }

  public function test_multiple_filters_and_search_work_together()
  {
    // Test that multiple filters and a search works at the same time.

    // Arrange: create rescues that match and don't match the filters
    Rescue::factory()->available()->create([
      'name' => 'Buddy',
      'sex' => 'male',
      'size' => 'small',
    ]);

    // Non-matching rescues
    Rescue::factory()->available()->count(2)->create(['sex' => 'female', 'size' => 'small']);
    Rescue::factory()->available()->count(2)->create(['sex' => 'male', 'size' => 'large']);
    Rescue::factory()->available()->count(2)->create(['sex' => 'male', 'size' => 'small']);

    // Act: apply filters and search query
    $response = $this->get(route('adoption.index', [
      'sex' => 'male',
      'size' => 'small',
      'search' => 'Buddy',
    ]));

    // Assert: Inertia props match
    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')
        ->where('filters.sex', 'male')
        ->where('filters.size', 'small')
        ->where('filters.search', 'Buddy')
        ->has('adoptables.data', 1) // only Buddy should match
        ->where('adoptables.data.0.name', 'Buddy')
        ->where('adoptables.data.0.sex', 'male')
      ->where('adoptables.data.0.size', 'small')
    );
  }

  public function test_combining_filters_respect_visibility_rules()
  {
    // Test taht trashed records still hidden for non-admin/staff users

    // Arrange
    $visible = Rescue::factory()->available()->count(3)->create([
      'sex' => 'male',
      'size' => 'medium',
    ]);

    $trashed = Rescue::factory()->available()->trashed()->count(4)->create([
      'sex' => 'male',
      'size' => 'medium',
    ]);

    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();

    // Act & Assert for regular user
    $this->actingAs($user);
    $userResponse = $this->get(route('adoption.index', [
      'sex' => 'male',
      'size' => 'medium',
    ]));

    $userResponse->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')
       ->has('adoptables.data', 3) // only non-trashed
        ->where('filters.sex', 'male')
        ->where('filters.size', 'medium')
      ->where('adoptables.data', function ($adoptables) {
        // Ensure none are soft deleted
        return collect($adoptables)->every(fn ($r) => $r['deleted_at'] === null);
      })
    );

    // Act & Assert for admin
    $this->actingAs($admin);
    $adminResponse = $this->get(route('adoption.index', [
      'sex' => 'male',
      'size' => 'medium',
      'with_trashed' => true,
    ]));

    $adminResponse->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')
        ->has('adoptables.data', 7) // visible + trashed
        ->where('filters.sex', 'male')
      ->where('filters.size', 'medium')
    );
  }

  public function test_search_and_filter_results_are_paginated_with_9_items_page()
  {
    // Arrange: Create 15 rescues that match search + filter
    Rescue::factory()->available()->count(15)->create([
      'sex' => 'male',
      'name' => 'Buddy',
    ]);

    // Create some that should NOT match (different sex or name)
    Rescue::factory()->count(5)->available()->create(['sex' => 'female', 'name' => 'Mittens']);

    // Act: Perform GET with both search and filter
    $response = $this->get(route('adoption.index', [
      'search' => 'buddy',
      'sex' => 'male',
    ]));

    // Assert: Response OK & paginated with 9 items only
    $response->assertStatus(200);

    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')
        ->where('filters.search', 'buddy')
        ->where('filters.sex', 'male')
        ->has('adoptables.data', 9) // 9 items per page
      ->has('adoptables.links') // pagination links should exist
    );
  }

  public function test_query_string_parameters_are_preserved_in_pagination_links()
  {
    // Arrange: Create more rescues than one page
    Rescue::factory()->available()->count(20)->create([
      'sex' => 'female',
      'name' => 'Luna',
    ]);

    // Act: Apply search and filter
    $response = $this->get(route('adoption.index', [
      'search' => 'luna',
      'sex' => 'female',
      'page' => 2,
    ]));

    // Assert: Check that pagination links include preserved query params
    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')
        ->where('filters.search', 'luna')
        ->where('filters.sex', 'female')
        ->has('adoptables.links') // just ensure 'links' exists
      ->where('adoptables.links', function ($links) {
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
    $rescueWithApps = Rescue::factory()->available()->create();

    AdoptionApplication::factory()->count(3)->create([
      'rescue_id' => $rescueWithApps->id,
    ]);
    $response = $this->get(route('adoption.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')
      ->has('adoptables.data')
      ->where('adoptables.data', function ($rescues) use ($rescueWithApps) {
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

    $response = $this->get(route('adoption.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')
        ->has('user')
        ->where('user.id', $user->id)
      ->where('user.isAdminOrStaff', false)
    );

    // Simulate admin user logged in
    $user = User::factory()->admin()->create();

    $this->actingAs($user);

    $response = $this->get(route('adoption.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')
        ->has('user')
        ->where('user.id', $user->id)
      ->where('user.isAdminOrStaff', true)
    );

    //Simulate staff
    $user = User::factory()->staff()->create();

    $this->actingAs($user);

    $response = $this->get(route('adoption.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')
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

    $response = $this->get(route('adoption.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')
        ->has('user')
        ->where('user.id', $user->id)
      ->where('user.canAdopt', true)
    );

    //Simulate admni logged in
    $admin = User::factory()->admin()->create();

    $address = Address::factory()->create([
      'user_id' => $admin->id,
    ]);

    $household = Household::factory()->create([
      'user_id' => $admin->id,
    ]);

    $this->actingAs($admin);

    $response = $this->get(route('adoption.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')
        ->has('user')
        ->where('user.id', $admin->id)
      ->where('user.canAdopt', false)
    );
  }

  public function test_user_cannot_adopt_if_missing_address_or_household()
  {
    $user = User::factory()->create();
    // no address or household created

    $this->actingAs($user);

    $response = $this->get(route('adoption.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')
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

    $response = $this->get(route('adoption.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')
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

    $response = $this->get(route('adoption.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')
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
    ];

    $response = $this->get(route('adoption.index', $params));

    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')
        ->where('filters.search', 'buddy')
        ->where('filters.sex', 'male')
      ->where('filters.size', 'small')
    );
  }

  public function test_user_is_null_in_response_for_guest_users()
  {
    $response = $this->get(route('adoption.index'));

    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')->where('user', null)
    );
  }

  public function test_empty_string_search_returns_all_available_rescues()
  {
    $rescues = Rescue::factory()->available()->count(5)->create();
    
    $response = $this->get(route('adoption.index', ['search' => '']));
    
    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')
        ->where('filters.search', null)
       ->has('adoptables.data', 5)
    );
  }

  public function test_search_handles_special_characters_safely()
  {
    $rescue = Rescue::factory()->available()->create(['name' => "O'Malley"]);
    
    $response = $this->get(route('adoption.index', ['search' => "O'Mal"]));
    
    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')
        ->has('adoptables.data')
      ->where('adoptables.data.0.name', "O'Malley")
    );
  }

  public function test_requesting_page_beyond_available_pages_returns_empty_results()
  {
    Rescue::factory()->available()->count(5)->create();
    
    $response = $this->get(route('adoption.index', ['page' => 999]));
    
    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')->has('adoptables.data', 0)
    );
  }

  public function test_search_with_url_encoded_characters()
  {
    $rescue = Rescue::factory()->available()->create(['name' => 'Max & Ruby']);
    
    $response = $this->get(route('adoption.index', ['search' => 'Max & Ruby']));
    
    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')->has('adoptables.data', 1)
    );
  }

  public function test_rescue_without_applications_shows_zero_count()
  {
    $rescue = Rescue::factory()->available()->create();
    
    $response = $this->get(route('adoption.index'));
    
    $response->assertInertia(fn ($page) =>
      $page->component('Adoption/Index')
        ->where('adoptables.data', function ($rescues) use ($rescue) {
          return collect($rescues)->contains(fn ($r) => $r['id'] === $rescue->id && $r['adoption_applications_count'] === 0
        );
      })
    );
  }
}
