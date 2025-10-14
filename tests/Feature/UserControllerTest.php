<?php

namespace Tests\Feature;


use App\Models\User;
use App\Models\Address;
use App\Models\Donation;
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

    Report::factory()->count(2)->for($user)->found()->trashed()->create();
    Report::factory()->count(2)->for($user)->lost()->trashed()->create();

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
      ->has('reports.data')
      ->where('reports.data.0.breed', $searchTerm)
    );

    // 🧩 Act & Assert - Type filter
    $response = $this->get(route('users.myReports', ['type' => 'lost']));
    $response->assertOk();
    $this->assertTrue(
        Report::where('user_id', $user->id)->where('type', 'lost')->exists()
    );

    // For type filter - verify only correct type returned
    $response->assertInertia(fn (AssertableInertia $page) =>
      $page->has('reports.data')->etc()
      ->where('reports.data.0.type', 'lost') // check first item
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
        ->where('sort', null)
      )
      ->has('reports.data')
      ->where('reports.data.0.status', 'active')
    );

    // 🧭 Act & Assert - Sorting ASC
    $response = $this->get(route('users.myReports', ['sort' => 'asc']));
    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) =>
      $page->has('filters', fn ($filters) => 
        $filters->where('sort', 'asc')
          ->where('search', null)
          ->where('type', null)
          ->where('status', null)
      )
    );

    // Get Inertia props and extract reports
    $reports = $response->viewData('page')['props']['reports']['data'];
    $this->assertGreaterThan(1, count($reports));
    $firstTimestamp = strtotime($reports[0]['created_at']);
    $lastTimestamp = strtotime($reports[count($reports) - 1]['created_at']);
    $this->assertLessThanOrEqual($lastTimestamp, $firstTimestamp, 'Reports should be sorted in ascending order (oldest first)');

    // 🧭 Act & Assert - Sorting DESC
    $response = $this->get(route('users.myReports', ['sort' => 'desc']));
    $response->assertOk();

    $reports = $response->viewData('page')['props']['reports']['data'];
    $this->assertGreaterThan(1, count($reports));
    $firstTimestamp = strtotime($reports[0]['created_at']);
    $lastTimestamp = strtotime($reports[count($reports) - 1]['created_at']);
    $this->assertGreaterThanOrEqual($lastTimestamp, $firstTimestamp, 'Reports should be sorted in descending order (newest first)');
  }

  public function test_show_user_myDonations_page()
  {
    // $user = User::factory()->create();

    // //Pending in-kind donations
    // Donation::factory()->count(5)->for($user)->inKind()->create();

    // //Accepted in-kind donations with Trashed
    // Donation::factory()->count(5)->for($user)->inKind()->accepted()->create();
    // Donation::factory()->count(5)->for($user)->inKind()->accepted()->trashed()->create();
    
    // //Rejected in-kind donation with Trashed
    // Donation::factory()->count(5)->for($user)->inKind()->rejected()->create();
    // Donation::factory()->count(5)->for($user)->inKind()->rejected()->trashed()->create();

    // //Cancelled in-kind donation with Trashed
    // Donation::factory()->count(5)->for($user)->inKind()->cancelled()->create();
    // Donation::factory()->count(5)->for($user)->inKind()->cancelled()->trashed()->create();

    // //Accepted Monetary Donations
    // Donation::factory()->count(5)->for($user)->monetary()->accepted()->create();
    // Donation::factory()->count(5)->for($user)->monetary()->accepted()->trashed()->create();

    // $this->actingAs($user);

    // // Act - base request (no filters)
    // $response = $this->from('/')->get(route('users.myDonations', $user));

    // // Assert - page loads correctly
    // $response->assertOk();

    // $response->assertInertia(fn (AssertableInertia $page) => $page
    //     ->component('User/MyDonations')
    //     ->has('user', fn ($userProps) => $userProps
    //         ->where('id', $user->id)
    //         ->etc()
    //     )
    //     ->has('donations.data', 5) // Paginated to 5 items per page
    //     ->has('filters', fn ($filters) => $filters
    //       ->where('search', null)
    //       ->where('donation_type', null)
    //       ->where('status', null)
    //       ->where('sort', null)
    //       ->etc()
    //     )
    //     ->where('previousUrl', url('/'))
    // );

    // $this->assertAuthenticated();

    // $this->assertEquals(25,Donation::where('user_id',$user->id)->count(),'Total donations count mismatch.');
    // $this->assertEquals(45,Donation::where('user_id',$user->id)->withTrashed()->count(),'Total donations count mismatch with trashed.');

    // $this->assertEquals(5,Donation::where('status','pending')->where('user_id',$user->id)->count(),'Pending in-kind donations count mismatch.');

    // $this->assertEquals(5,Donation::where('status','accepted')->where('user_id',$user->id)->where('donation_type','in-kind')->count(),'Accepted in-kind donations count mismatch.');
    // $this->assertEquals(10,Donation::where('status','accepted')->where('user_id',$user->id)->where('donation_type','in-kind')->withTrashed()->count(),'Trashed accepted in-kind donations count mismatch.');

    // $this->assertEquals(5,Donation::where('status','rejected')->where('user_id',$user->id)->where('donation_type','in-kind')->count(),'Rejected in-kind donations count mismatch.');
    // $this->assertEquals(10,Donation::where('status','rejected')->where('user_id',$user->id)->where('donation_type','in-kind')->withTrashed()->count(),'Trashed rejected in-kind donations count mismatch.');

    // $this->assertEquals(5,Donation::where('status','cancelled')->where('user_id',$user->id)->where('donation_type','in-kind')->count(),'Cancelled in-kind donations count mismatch.');
    // $this->assertEquals(10,Donation::where('status','cancelled')->where('user_id',$user->id)->where('donation_type','in-kind')->withTrashed()->count(),'Trashed cancelled in-kind donations count mismatch.');
    
    // $this->assertEquals(5,Donation::where('user_id',$user->id)->where('donation_type','monetary')->count(),'Total monetary donations count mismatch.');
    // $this->assertEquals(10,Donation::where('user_id',$user->id)->where('donation_type','monetary')->withTrashed()->count(),'Total donations count mismatch with trashed.');

    // // 🔍 Act & Assert - Search filter
    // $targetDonation = Donation::where('user_id', $user->id)->first();
    // $searchTerm = $targetDonation->donation_type; // example searchable field

    // $response = $this->get(route('users.myDonations', ['search' => $searchTerm]));
    // $response->assertOk();
    // $response->assertInertia(fn (AssertableInertia $page) =>
    // $page->has('filters', fn ($filters) =>
    //   $filters
    //     ->where('search', $searchTerm)
    //     ->where('donation_type', null)
    //     ->where('status', null)
    //     ->where('sort', null)
    //   )
    // );

    // // 🧩 Act & Assert - Type filter
    // $response = $this->get(route('users.myDonations', ['donation_type' => 'in-kind']));
    // $response->assertOk();
    // $this->assertTrue(
    //     Donation::where('user_id', $user->id)->where('donation_type', 'in-kind')->exists()
    // );

    // // ⚙️ Act & Assert - Status filter
    // $response = $this->get(route('users.myDonations', ['status' => 'accepted']));
    // $response->assertOk();
    // $response->assertInertia(fn (AssertableInertia $page) =>
    //     $page->has('filters', fn ($filters) => 
    //     $filters
    //     ->where('status', 'accepted')
    //     ->where('search', null)
    //     ->where('donation_type', null)
    //     ->where('sort', null))
    // );

    // // 🧭 Act & Assert - Sorting
    // $response = $this->get(route('users.myDonations', ['sort' => 'asc']));
    // $response->assertOk();
    // $response->assertInertia(fn (AssertableInertia $page) =>
    //   $page->has('filters', fn ($filters) => 
    //     $filters
    //     ->where('sort', 'asc')
    //     ->where('search', null)
    //     ->where('donation_type', null)
    //     ->where('status', null)
    //   )
    // );
  }
}
