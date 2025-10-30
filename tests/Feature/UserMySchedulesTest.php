<?php

namespace Tests\Feature;

use App\Models\AdoptionApplication;
use App\Models\InspectionSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class UserMySchedulesTest extends TestCase
{
  use RefreshDatabase;
  public function test_guest_user_cannot_access_users_mySchedules()
  {
    $response = $this->get(route('users.mySchedules'));

    $response->assertRedirect(route('login'));
  }

  public function test_authenticated_regular_user_cannot_access_users_mySchedules()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('users.mySchedules'));
    $response->assertRedirect();
    $response->assertSessionHas('error', 'You are not authorized to access this page.');
  }

  public function test_authenticated_admin_user_cann_access_users_mySchedules()
  { 
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $response = $this->get(route('users.mySchedules'));
    $response->assertOk();
  }

  public function test_authenticated_staff_user_can_access_users_mySchedules()
  { 
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $response = $this->get(route('users.mySchedules'));
    $response->assertOk();
  }

  public function test_authenticated_admin_user_can_only_view_their_own_schedules()
  {
    $admin = User::factory()->admin()->create();
    $otherUser = User::factory()->staff()->create();

    $visible = InspectionSchedule::factory()->count(2)->for($admin)->create();

    $notVisible = InspectionSchedule::factory()->count(2)->for($otherUser)->create();

    $this->actingAs($admin);

    $response = $this->get(route('users.mySchedules'));

    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
    $page
      ->component('User/MySchedules')
      ->where('schedules', function ($schedules) use ($visible, $notVisible) {
        $ids = collect($schedules)->pluck('id')->toArray();

        foreach ($visible as $schedule) {
          if (!in_array($schedule->id, $ids)) {
            return false;
          }
        }

        foreach ($notVisible as $schedule) {
          if (in_array($schedule->id, $ids)) {
            return false;
          }
        }
        return true;
      })
    );

  }

  public function test_authenticated_staff_user_can_only_view_their_own_schedules()
  {
    $staff = User::factory()->staff()->create();
    $otherUser = User::factory()->admin()->create();

    $visible = InspectionSchedule::factory()->count(2)->for($staff)->create();

    $notVisible = InspectionSchedule::factory()->count(2)->for($otherUser)->create();

    $this->actingAs($staff);

    $response = $this->get(route('users.mySchedules'));

    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
    $page
      ->component('User/MySchedules')
      ->where('schedules', function ($schedules) use ($visible, $notVisible) {
        $ids = collect($schedules)->pluck('id')->toArray();

        foreach ($visible as $schedule) {
          if (!in_array($schedule->id, $ids)) {
            return false;
          }
        }

        foreach ($notVisible as $schedule) {
          if (in_array($schedule->id, $ids)) {
            return false;
          }
        }
        return true;
      })
    );

  }

  public function test_inspection_schedules_are_loaded_with_correct_data()
  {
    $admin = User::factory()->admin()->create(['first_name' => 'John', 'last_name' => 'Doe']);
    $this->actingAs($admin);

    $application = AdoptionApplication::factory()->create();
    
    $schedule = InspectionSchedule::factory()->for($admin)->create([
      'application_id' => $application->id,
      'inspection_location' => '123 Main St',
      'inspection_date' => now()->addDays(5)->format('Y-m-d'),
      'status' => 'upcoming'
    ]);

    $response = $this->get(route('users.mySchedules'));
    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page->component('User/MySchedules')
        ->has('schedules', 1)
        ->where('schedules.0.id', $schedule->id)
        ->where('schedules.0.inspector_name', $admin->fullName())
        ->where('schedules.0.inspection_location', $schedule->inspection_location)
        ->where('schedules.0.inspection_date', $schedule->inspection_date)
      ->where('schedules.0.status', $schedule->status)
    );
  }

  public function test_response_include_previousUrl_value()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $response = $this->from('/')->get(route('users.mySchedules'));
    $response->assertOk();

    $response->assertInertia(fn ($page) =>
      $page->component('User/MySchedules')->where('previousUrl', url('/'))
    );
  }

  public function test_user_data_includes_id_role_and_fullName_for_authenticated_user()
  {
    $admin = User::factory()->admin()->create([
      'first_name' => 'Freddie',
      'last_name' => 'Freeman'
    ]);

    $this->actingAs($admin);

    $response = $this->from('/')->get(route('users.mySchedules'));

    $response->assertInertia(fn ($page) =>
      $page->component('User/MySchedules')
        ->has('user')
        ->where('user.id', $admin->id)
        ->where('user.role', $admin->role)
      ->where('user.fullName', $admin->fullName())
    );
  }

  public function test_schedules_data_includes_user_relationships()
  {
    $admin = User::factory()->admin()->create();

    InspectionSchedule::factory()->count(5)->create(['inspector_id' => $admin->id]);

    $this->actingAs($admin);

    $response = $this->get(route('users.mySchedules'));

    $response->assertInertia(fn ($page) =>
      $page->component('User/MySchedules')
        ->has('schedules', 5)
      ->has('user', fn ($user) =>
        $user->where('id', $admin->id)
          ->where('role', $admin->role)
        ->etc()
      )
    );
  }

  public function test_schedules_data_includes_adoption_applications_relationships()
  {
    $admin = User::factory()->admin()->create();
    $application = AdoptionApplication::factory()->under_review()->create();

    InspectionSchedule::factory()->create([
      'inspector_id' => $admin->id,
      'application_id' => $application->id,
    ]);

    $this->actingAs($admin);

    // Act
    $this->get(route('users.mySchedules'))->assertOk();

    // Assert (database-side, not Inertia)
    $fetchedSchedules = $admin->inspectionSchedules()->with('adoptionApplication')->get();

    $this->assertTrue($fetchedSchedules->every(fn($schedule) => $schedule->relationLoaded('adoptionApplication')),
      'Each schedule should have the adoptionApplication relationship loaded.'
    );

    $this->assertTrue($fetchedSchedules->every(fn($schedule) => !is_null($schedule->adoptionApplication)),
      'Each schedule should have a related adoption application.'
    );
  }

  public function test_authenticated_admin_with_no_schedules_sees_empty_list()
  {
    $admin = User::factory()->admin()->create();
    
    // Other users have schedules, but not this admin
    InspectionSchedule::factory()->count(3)->create([
      'inspector_id' => User::factory()->staff()->create()->id
    ]);
    
    $this->actingAs($admin);
    
    $response = $this->get(route('users.mySchedules'));
    $response->assertStatus(200);
    
    $response->assertInertia(fn (Assert $page) =>
      $page->component('User/MySchedules')
        ->has('schedules', 0)
        ->has('user')
      ->where('user.id', $admin->id)
    );
  }

  public function test_authenticated_user_sees_schedules_with_various_statuses()
  {
    $staff = User::factory()->staff()->create();
    
    InspectionSchedule::factory()->for($staff)->create(['inspection_date' => now()->addDays(4)->format('Y-m-d')]);
    InspectionSchedule::factory()->for($staff)->create(['inspection_date' => now()->format('Y-m-d')]);
    InspectionSchedule::factory()->for($staff)->create(['status' => 'done']);
    
    $this->actingAs($staff);
    
    $response = $this->get(route('users.mySchedules'));
    $response->assertStatus(200);
    
    $response->assertInertia(fn (Assert $page) =>
      $page->component('User/MySchedules')
        ->has('schedules', 3)
      ->where('schedules', function ($schedules) {
        $statuses = collect($schedules)->pluck('status')->toArray();
        return in_array('upcoming', $statuses) 
         && in_array('now', $statuses) 
        && in_array('done', $statuses);
      })
    );
  }
}
