<?php

namespace Tests\Feature;


use App\Models\User;
use App\Models\Address;
use App\Models\Household;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Report;
class UserControllerTest extends TestCase
{
  use RefreshDatabase;
  /**
    * A basic feature test example.
  */

  // public function test_example(): void
  // {
  //   $response = $this->get('/');

  //   $response->assertStatus(200);
  // }

  public function test_it_displays_the_user_show_page_with_correct_data()
  {
    // Arrange: create a user with related models
    $user = User::factory()
      ->has(Address::factory())
      ->has(Household::factory())
    ->admin()->create();

    $this->actingAs($user);

    // Act: make a GET request to the show route
    $response = $this->from('/dashboard')->get(route('users.show', $user));

    // Assert: response is OK and Inertia view is correct
    $response->assertOk();

    $response->assertInertia(fn (AssertableInertia $page) => $page
      ->component('User/Show')
      ->has('user', fn ($userProps) => $userProps
        ->where('id', $user->id)
        ->etc()
      )
      ->where('previousUrl', url('/dashboard'))
    );
  }

  public function test_show_user_myReports_page()
  {
    $user = User::factory()->create();

    Report::factory()->count(3)->for($user)->found()->create();
    Report::factory()->count(3)->for($user)->lost()->create();

    Report::factory()->count(2)->for($user)->trashed()->create();
    Report::factory()->count(2)->for($user)->trashed()->create();

    $this->actingAs($user);

    // Act - base request (no filters)
    $response = $this->from('/')->get(route('users.myReports', $user));

    // Assert - page loads correctly
    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('User/MyReports')
        ->has('user', fn ($userProps) => $userProps
            ->where('id', $user->id)
            ->etc()
        )
        ->has('reports.data', 5) // Paginated to 5 items per page
        ->has('filters', fn ($filters) => $filters
            ->where('search', null)
            ->where('type', null)
            ->where('status', null)
            ->where('sort', null)
            ->etc()
        )
        ->where('previousUrl', url('/'))
    );

    // Database assertions
    $this->assertAuthenticated();
    $this->assertEquals(6, Report::where('user_id', $user->id)->count(), 'Active reports mismatch.');
    $this->assertEquals(4, Report::onlyTrashed()->where('user_id', $user->id)->count(), 'Trashed reports mismatch.');
    $this->assertEquals(10, Report::withTrashed()->where('user_id', $user->id)->count(), 'Total reports mismatch.');
    $this->assertEquals(3, Report::where('type', 'lost')->count(), 'Lost reports mismatch.');
    $this->assertEquals(3, Report::where('type', 'found')->count(), 'Found reports mismatch.');

    // 🔍 Act & Assert - Search filter
    $targetReport = Report::where('user_id', $user->id)->first();
    $searchTerm = $targetReport->breed; // example searchable field

    $response = $this->get(route('users.myReports', ['search' => $searchTerm]));
    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) =>
    $page->has('filters', fn ($filters) =>
      $filters
        ->where('search', $searchTerm)
        ->where('type', null)
        ->where('status', null)
        ->where('sort', null)
      )
    );

    // 🧩 Act & Assert - Type filter
    $response = $this->get(route('users.myReports', ['type' => 'lost']));
    $response->assertOk();
    $this->assertTrue(
        Report::where('user_id', $user->id)->where('type', 'lost')->exists()
    );

    // ⚙️ Act & Assert - Status filter
    $response = $this->get(route('users.myReports', ['status' => 'active']));
    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) =>
        $page->has('filters', fn ($filters) => 
        $filters
        ->where('status', 'active')
        ->where('search', null)
        ->where('type', null)
        ->where('sort', null))
    );

    // 🧭 Act & Assert - Sorting
    $response = $this->get(route('users.myReports', ['sort' => 'asc']));
    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) =>
      $page->has('filters', fn ($filters) => 
        $filters
        ->where('sort', 'asc')
        ->where('search', null)
        ->where('type', null)
        ->where('status', null)
      )
    );
  }
}
