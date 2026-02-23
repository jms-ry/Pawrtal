<?php

namespace Tests\Feature;

use App\Models\AdoptionApplication;
use App\Models\InspectionSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InspectionScheduleTest extends TestCase
{
  use RefreshDatabase;

  // Inspection Schedule model tests
  // -------------------------------------------------------------------------
  // Helpers
  // -------------------------------------------------------------------------

  private function makeSchedule(array $attributes = []): InspectionSchedule
  {
    return InspectionSchedule::factory()->make($attributes);
  }

  private function createSchedule(array $attributes = []): InspectionSchedule
  {
    return InspectionSchedule::factory()->create($attributes);
  }

  // -------------------------------------------------------------------------
  // inspectionLocation()
  // -------------------------------------------------------------------------

  public function test_inspection_location_returns_headline_cased_location(): void
  {
    $schedule = $this->makeSchedule(['inspection_location' => 'cebu city']);

    $this->assertSame('Cebu City', $schedule->inspectionLocation());
  }

  public function test_inspection_location_handles_all_caps_input(): void
  {
    $schedule = $this->makeSchedule(['inspection_location' => 'MANDAUE CITY']);

    $this->assertSame('Mandaue City', $schedule->inspectionLocation());
  }

  // -------------------------------------------------------------------------
  // inspectorName()
  // -------------------------------------------------------------------------

  public function test_inspector_name_returns_full_name_when_inspector_id_is_set(): void
  {
    $inspector = User::factory()->create([
      'first_name' => 'Jane',
      'last_name'  => 'Smith',
    ]);

    $schedule = $this->createSchedule(['inspector_id' => $inspector->id]);

    $this->assertSame($inspector->fullName(), $schedule->inspectorName());
  }

  public function test_inspector_name_returns_null_when_inspector_id_is_null(): void
  {
    $schedule = $this->makeSchedule(['inspector_id' => null]);

    $this->assertNull($schedule->inspectorName());
  }

  // -------------------------------------------------------------------------
  // inspectionDate()
  // -------------------------------------------------------------------------

  public function test_inspection_date_returns_correctly_formatted_date(): void
  {
    $schedule = $this->makeSchedule(['inspection_date' => '2025-06-15']);

    $this->assertSame('Jun 15, 2025', $schedule->inspectionDate());
  }

  public function test_inspection_date_formats_single_digit_day_correctly(): void
  {
    $schedule = $this->makeSchedule(['inspection_date' => '2025-01-05']);

    $this->assertSame('Jan 05, 2025', $schedule->inspectionDate());
  }

  // -------------------------------------------------------------------------
  // getCurrentStatus() / inspectionStatus()
  // -------------------------------------------------------------------------

  public function test_get_current_status_returns_done_regardless_of_date(): void
  {
    $schedule = $this->makeSchedule([
      'status'          => 'done',
      'inspection_date' => now()->subDays(10)->toDateString(),
    ]);

    $this->assertSame('done', $schedule->getCurrentStatus());
  }

  public function test_get_current_status_returns_cancelled_regardless_of_date(): void
  {
    $schedule = $this->makeSchedule([
      'status'          => 'cancelled',
      'inspection_date' => now()->addDays(10)->toDateString(),
    ]);

    $this->assertSame('cancelled', $schedule->getCurrentStatus());
  }

  public function test_get_current_status_returns_now_when_inspection_date_is_today(): void
  {
    $schedule = $this->makeSchedule([
      'status'          => 'upcoming',
      'inspection_date' => now()->toDateString(),
    ]);

    $this->assertSame('now', $schedule->getCurrentStatus());
  }

  public function test_get_current_status_returns_missed_when_inspection_date_is_in_the_past(): void
  {
    $schedule = $this->makeSchedule([
      'status'=> 'upcoming',
      'inspection_date' => now()->subDay()->toDateString(),
    ]);

    $this->assertSame('missed', $schedule->getCurrentStatus());
  }

  public function test_get_current_status_returns_upcoming_when_inspection_date_is_in_the_future(): void
  {
    $schedule = $this->makeSchedule([
      'status'          => 'upcoming',
      'inspection_date' => now()->addDay()->toDateString(),
    ]);

     $this->assertSame('upcoming', $schedule->getCurrentStatus());
  }

  public function test_inspection_status_delegates_to_get_current_status(): void
  {
    $schedule = $this->makeSchedule([
      'status'          => 'upcoming',
      'inspection_date' => now()->addDay()->toDateString(),
    ]);

    $this->assertSame($schedule->getCurrentStatus(), $schedule->inspectionStatus());
  }

  // -------------------------------------------------------------------------
  // boot() — auto status assignment on create
  // -------------------------------------------------------------------------

  public function test_boot_sets_status_to_now_when_inspection_date_is_today_and_status_is_null(): void
  {
    $schedule = $this->createSchedule([
      'status'          => null,
      'inspection_date' => now()->toDateString(),
    ]);

    $this->assertSame('now', $schedule->status);
  }

  public function test_boot_sets_status_to_missed_when_inspection_date_is_in_the_past_and_status_is_null(): void
  {
    $schedule = $this->createSchedule([
      'status'          => null,
      'inspection_date' => now()->subDay()->toDateString(),
    ]);

    $this->assertSame('missed', $schedule->status);
  }

  public function test_boot_sets_status_to_upcoming_when_inspection_date_is_in_the_future_and_status_is_null(): void
  {
    $schedule = $this->createSchedule([
      'status'          => null,
      'inspection_date' => now()->addDay()->toDateString(),
    ]);

    $this->assertSame('upcoming', $schedule->status);
  }

  public function test_boot_does_not_override_status_when_status_is_already_set(): void
  {
    $schedule = $this->createSchedule([
      'status'          => 'done',
      'inspection_date' => now()->toDateString(), // would be 'now' if auto-assigned
    ]);

    $this->assertSame('done', $schedule->status);
  }

  public function test_boot_does_not_override_cancelled_status_when_already_set(): void
  {
    $schedule = $this->createSchedule([
      'status'          => 'cancelled',
      'inspection_date' => now()->addDays(5)->toDateString(), // would be 'upcoming' if auto-assigned
    ]);

    $this->assertSame('cancelled', $schedule->status);
  }
}