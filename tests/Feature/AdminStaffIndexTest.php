<?php

namespace Tests\Feature;

use App\Models\AdoptionApplication;
use App\Models\Donation;
use App\Models\InspectionSchedule;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Rescue;
use Inertia\Testing\AssertableInertia as Assert;

class AdminStaffIndexTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannot_access_dashboard()
  {
    $response = $this->get('/dashboard');

    $response->assertRedirect(route('login'));
  }

  public function test_regular_user_cannot_access_dashboard()
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/dashboard');

    $response->assertRedirect('/');
    $response->assertSessionHas('error', 'You do not have authorization. Access denied!');
  }
  
  public function test_admin_user_can_access_dashboard()
  {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

   $response = $this->get('/dashboard');

    $response->assertStatus(200);
  }

  public function test_staff_user_can_access_dashboard()
  {
    $staff = User::factory()->staff()->create();
    $this->actingAs($staff);

    $response = $this->get('/dashboard');

    $response->assertStatus(200);
  }

  public function test_admin_user_sees_all_rescues_including_trashed()
  { 
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $nonTrashed = Rescue::factory()->count(3)->create();
    $trashed = Rescue::factory()->trashed()->count(2)->create();

    $response = $this->get('/dashboard');
    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page->component('AdminStaff/Dashboard')->has('rescues', 5) // 3 + 2
    );
  }

  public function test_staff_user_sees_all_rescues_including_trashed()
  { 
    $staff = User::factory()->staff()->create();
    $this->actingAs($staff);

    $nonTrashed = Rescue::factory()->count(3)->create();
    $trashed = Rescue::factory()->trashed()->count(2)->create();

    $response = $this->get('/dashboard');
    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page->component('AdminStaff/Dashboard')->has('rescues', 5) // 3 + 2
    );
  }

  public function test_admin_user_sees_all_reports_including_trashed()
  {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $nonTrashed = Report::factory()->count(4)->create();
    $trashed = Report::factory()->trashed()->count(3)->create();

    $response = $this->get('/dashboard');
    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page->component('AdminStaff/Dashboard')->has('reports', 7) // 4 + 3
    );
  }

  public function test_staff_user_sees_all_reports_including_trashed()
  {
    $staff = User::factory()->staff()->create();
    $this->actingAs($staff);

    $nonTrashed = Report::factory()->count(4)->create();
    $trashed = Report::factory()->trashed()->count(3)->create();

    $response = $this->get('/dashboard');
    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page->component('AdminStaff/Dashboard')->has('reports', 7) // 4 + 3
    );
  }

  public function test_admin_user_sees_all_donations_including_trashed()
  {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $nonTrashed = Donation::factory()->count(5)->create();
    $trashed = Donation::factory()->trashed()->count(2)->create();

    $response = $this->get('/dashboard');
    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page->component('AdminStaff/Dashboard')->has('donations', 7) // 5 + 2
    );
  }

  public function test_staff_user_sees_all_donations_including_trashed()
  {
    $staff = User::factory()->staff()->create();
    $this->actingAs($staff);

    $nonTrashed = Donation::factory()->count(5)->create();
    $trashed = Donation::factory()->trashed()->count(2)->create();

    $response = $this->get('/dashboard');
    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page->component('AdminStaff/Dashboard')->has('donations', 7) // 5 + 2
    );
  }

  public function test_admin_user_sees_all_adoption_applications_including_trashed()
  {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $nonTrashed = AdoptionApplication::factory()->count(3)->create();
    $trashed = AdoptionApplication::factory()->trashed()->count(2)->create();

    $response = $this->get('/dashboard');
    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page->component('AdminStaff/Dashboard')->has('applications', 5) // 3 + 2
    );
  }

  public function test_staff_user_sees_all_adoption_applications_including_trashed()
  {
    $staff = User::factory()->staff()->create();
    $this->actingAs($staff);

    $nonTrashed = AdoptionApplication::factory()->count(3)->create();
    $trashed = AdoptionApplication::factory()->trashed()->count(2)->create();

    $response = $this->get('/dashboard');
    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page->component('AdminStaff/Dashboard')->has('applications', 5) // 3 + 2
    );
  }

  public function test_inspection_schedules_are_loaded_with_correct_data()
  {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $inspector = User::factory()->staff()->create(['first_name' => 'John', 'last_name' => 'Doe']);
    $application = AdoptionApplication::factory()->create();
    
    $schedule = InspectionSchedule::factory()->create([
      'inspector_id' => $inspector->id,
      'application_id' => $application->id,
      'inspection_location' => '123 Main St',
      'inspection_date' => now()->addDays(5)->format('Y-m-d'),
      'status' => 'upcoming'
    ]);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page->component('AdminStaff/Dashboard')
        ->has('schedules', 1)
        ->where('schedules.0.id', $schedule->id)
        ->where('schedules.0.inspector_name', 'John Doe')
        ->where('schedules.0.inspection_location', '123 Main St')
        ->where('schedules.0.inspection_date', $schedule->inspection_date)
      ->where('schedules.0.status', 'upcoming')
    );
  }

  public function test_response_includes_showBackNav_value()
  {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $response = $this->from('/')->get('/dashboard');
    $response->assertStatus(200);

    $response->assertInertia(fn ($page) =>
      $page->component('AdminStaff/Dashboard')->where('showBackNav', true)
    );
  }

  public function test_response_includes_previousUrl_value()
  {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $response = $this->from('/')->get('/dashboard');
    $response->assertStatus(200);

    $response->assertInertia(fn ($page) =>
      $page->component('AdminStaff/Dashboard')->where('previousUrl', url('/'))
    );
  }

  public function test_dashboard_works_when_no_data_exists()
  {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page->component('AdminStaff/Dashboard')
        ->has('rescues', 0)
        ->has('reports', 0)
        ->has('donations', 0)
        ->has('applications', 0)
      ->has('schedules', 0)
    );
  }

  public function test_showBackNav_is_false_when_coming_from_login()
  {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $response = $this->from('/login')->get('/dashboard');
    $response->assertStatus(200);

    $response->assertInertia(fn ($page) =>
      $page->component('AdminStaff/Dashboard')->where('showBackNav', false)
    );
  }

  public function test_showBackNav_is_false_when_coming_from_register()
  {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $response = $this->from('/register')->get('/dashboard');
    $response->assertStatus(200);

    $response->assertInertia(fn ($page) =>
      $page->component('AdminStaff/Dashboard')->where('showBackNav', false)
    );
  }

  public function test_showBackNav_is_false_when_coming_from_dashboard()
  {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $response = $this->from('/dashboard')->get('/dashboard');
    $response->assertStatus(200);

    $response->assertInertia(fn ($page) =>
      $page->component('AdminStaff/Dashboard')->where('showBackNav', false)
    );
  }

  public function test_multiple_inspection_schedules_are_returned_correctly()
  {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $inspector1 = User::factory()->staff()->create();
    $inspector2 = User::factory()->admin()->create();
    
    InspectionSchedule::factory()->count(3)->create(['inspector_id' => $inspector1->id]);
    InspectionSchedule::factory()->count(2)->create(['inspector_id' => $inspector2->id]);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page->component('AdminStaff/Dashboard')->has('schedules', 5)
    );
  }
}
