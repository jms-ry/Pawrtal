<?php

namespace Tests\Feature;

use App\Models\AdoptionApplication;
use App\Models\InspectionSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
class InspectionScheduleStoreTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannot_store_inspection_schedule()
  {
    $application = AdoptionApplication::factory()->create();
    $inspector = User::factory()->staff()->create();

    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' =>fake()->date(),
    ];

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('inspection_schedules', 0);
  }

  public function test_regular_user_cannot_store_inspection_schedule()
  {
    $user = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($user);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertForbidden();
    $this->assertDatabaseCount('inspection_schedules', 0);
  }

  public function test_admin_user_can_store_inspection_schedule_for_pending_applications_only()
  {
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($admin);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Inspection schedule has been scheduled.');
  }

  public function test_admin_user_cannot_store_inspection_schedule_for_under_review_applications()
  {
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->under_review()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($admin);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('application_id');
    $this->assertDatabaseCount('inspection_schedules', 0);
  }

  public function test_admin_user_cannot_store_inspection_schedule_for_cancelled_applications()
  {
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->cancelled()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($admin);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('application_id');
    $this->assertDatabaseCount('inspection_schedules', 0);
  }

  public function test_admin_user_cannot_store_inspection_schedule_for_approved_applications()
  {
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->approved()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($admin);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('application_id');
    $this->assertDatabaseCount('inspection_schedules', 0);
  }

  public function test_admin_user_cannot_store_inspection_schedule_for_rejected_applications()
  {
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->rejected()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($admin);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('application_id');
    $this->assertDatabaseCount('inspection_schedules', 0);
  }

  public function test_staff_user_can_store_inspection_schedule_for_pending_applications_only()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Inspection schedule has been scheduled.');
  }

  public function test_staff_user_cannot_store_inspection_schedule_for_under_review_applications()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->under_review()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('application_id');
    $this->assertDatabaseCount('inspection_schedules', 0);
  }

  public function test_staff_user_cannot_store_inspection_schedule_for_cancelled_applications()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->cancelled()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('application_id');
    $this->assertDatabaseCount('inspection_schedules', 0);
  }

  public function test_staff_user_cannot_store_inspection_schedule_for_approved_applications()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->approved()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('application_id');
    $this->assertDatabaseCount('inspection_schedules', 0);
  }

  public function test_staff_user_cannot_store_inspection_schedule_for_rejected_applications()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->rejected()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('application_id');
    $this->assertDatabaseCount('inspection_schedules', 0);
  }

  public function test_missing_application_id_fails_validation()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      //'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('application_id');
  }

  public function test_non_pending_application_fails_validation()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->rejected()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('application_id');
  }

  public function test_missing_inspector_id_fails_validation()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      //'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('inspector_id');
  }

  public function test_non_staff_or_admin_inspector_id_fails_validation()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('inspector_id');
  }

  public function test_missing_inspection_location_fails_validation()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      //'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('inspection_location');
  }

  public function test_very_long_inspection_location_fails_validation()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();

    $veryLongText =  str_repeat('a', 65535);

    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => $veryLongText,
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('inspection_location');
  }

  public function test_missing_inspection_date_fails_validation()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      //'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('inspection_date');
  }

  public function test_invalid_inspection_date_format_fails_validation()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => 'not-a-date',
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('inspection_date');
  }
  public function test_inspection_date_outside_preffered_inspection_range_fails_validation()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(14)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('inspection_date');
  }

  public function test_inspection_date_within_preffered_inspection_range_passes_validation()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Inspection schedule has been scheduled.');
  }

  public function test_inspection_date_equals_preffered_inspection_start_date_passes_validation()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => $application->preferred_inspection_start_date,
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Inspection schedule has been scheduled.');
  }

  public function test_inspection_date_equals_preffered_inspection_end_date_passes_validation()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => $application->preferred_inspection_end_date,
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Inspection schedule has been scheduled.');
  }

  public function test_inspection_date_set_after_preffered_inspection_end_date_fails_validation()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => Carbon::parse($application->preferred_inspection_end_date)->addDays(7)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('inspection_date');
  }

  public function test_inspection_date_set_before_preffered_inspection_start_date_fails_validation()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => Carbon::parse($application->preferred_inspection_start_date)->subDays(7)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('inspection_date');
  }

  public function test_inspection_status_is_now_when_inspection_date_is_now()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Inspection schedule has been scheduled.');

    $schedule = InspectionSchedule::first();
    $this->assertEquals('now', $schedule->status, 'Status should be "now" when inspection date is today');
  }

  public function test_inspection_status_is_upcoming_when_inspection_date_is_in_the_future()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Inspection schedule has been scheduled.');

    $schedule = InspectionSchedule::first();
    $this->assertEquals('upcoming', $schedule->status, 'Status should be "upcoming" when inspection date is in the future');
  }

  public function test_storing_inspection_schedule_with_nonexistent_application_id_fails()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->rejected()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => 9999,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('application_id');
  }

  public function test_storing_inspection_schedule_with_nonexistent_inspector_id_fails()
  {
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->rejected()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    //$inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => 9999,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($staff);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertSessionHasErrors('inspector_id');
  }

  public function test_inspection_schedule_record_is_created_in_database_with_correct_data()
  {
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->staff()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($admin);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Inspection schedule has been scheduled.');

    $this->assertDatabaseHas('inspection_schedules',[
      'application_id' => $scheduleData['application_id'],
      'inspector_id' => $scheduleData['inspector_id'],
      'inspection_location' => $scheduleData['inspection_location'],
      'inspection_date' => $scheduleData['inspection_date']
    ]);
  }

  public function test_admin_user_can_be_assigned_as_inspector()
  { 
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    $inspector = User::factory()->admin()->create();


    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($admin);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Inspection schedule has been scheduled.');
  }

  public function test_application_status_changes_to_under_review_after_scheduling()
  {
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
      'status' => 'pending', // initial status
    ]);

    $inspector = User::factory()->admin()->create();

    $scheduleData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ];

    $this->actingAs($admin);

    $response = $this->post(route('inspection-schedules.store'), $scheduleData);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Inspection schedule has been scheduled.');

    $this->assertDatabaseHas('inspection_schedules', [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
    ]);

    $application->refresh();
    $this->assertEquals('under_review', $application->status);
  }

  public function test_cannot_create_duplicate_inspection_schedule_for_same_application()
  {
    $admin = User::factory()->admin()->create();
    $inspector = User::factory()->staff()->create();

    // Create an adoption application
    $application = AdoptionApplication::factory()->create([
      'user_id' => User::factory(),
      'preferred_inspection_start_date' => now()->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(7)->format('Y-m-d'),
      'status' => 'pending',
    ]);

    // Create an initial inspection schedule for that application
    InspectionSchedule::factory()->create([
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
      'status' => 'upcoming'
    ]);

    // Attempt to create another schedule for the same application
    $duplicateData = [
      'application_id' => $application->id,
      'inspector_id' => $inspector->id,
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now()->addDays(4)->format('Y-m-d'),
    ];

    $this->actingAs($admin);

    $response = $this->post(route('inspection-schedules.store'), $duplicateData);

    $response->assertSessionHasErrors('application_id');

    $this->assertEquals(1, InspectionSchedule::where('application_id', $application->id)->count());
  }

}
