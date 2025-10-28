<?php

namespace Tests\Feature;

use App\Models\InspectionSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InspectionScheduleUpdateTest extends TestCase
{
  use RefreshDatabase;

  /**The following test cases are for updating the statuses only.
   * TO-DO: Add test cases for when updating other details of inspection is implemented.
  */
  public function test_guest_user_cannot_update_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create();

    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'done'
    ]);

    $response->assertRedirect(route('login'));
  }

  // public function test_regular_user_cannot_update_inspection_schedule()
  // {
  //   $schedule = InspectionSchedule::factory()->create([
  //     'inspection_location' => 'inspection location'
  //   ]);

  //   $user = User::factory()->create();

  //   $this->actingAs($user);
    
  //   $response = $this->patch(route('inspection-schedules.update', $schedule), [
  //     'inspection_location' => 'updated'
  //   ]);

  //   $response->assertForbidden();
  // }

  public function test_regular_user_cannot_mark_inspection_schedule_as_done()
  {
    $schedule = InspectionSchedule::factory()->create([
      'status' => 'now'
    ]);
    
    $user = User::factory()->create();

    $this->actingAs($user);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'done'
    ]);

    $response->assertForbidden();
  }

  public function test_regular_user_cannot_cancel_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'status' => 'now'
    ]);
    
    $user = User::factory()->create();

    $this->actingAs($user);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'cancelled'
    ]);

    $response->assertForbidden();
  }

  public function test_admin_user_cannot_cancel_cancelled_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'status' => 'cancelled'
    ]);
    
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'cancelled'
    ]);

    $response->assertForbidden();
  }

  public function test_admin_user_cannot_cancel_missed_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'inspection_date' => now()->subDays(3)->format('Y-m-d'),
    ]);
    
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'cancelled'
    ]);

    $response->assertForbidden();
  }

  public function test_admin_user_cannot_cancel_done_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'status' => 'done'
    ]);
    
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'cancelled'
    ]);

    $response->assertForbidden();
  }

  public function test_admin_user_can_cancel_now_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'inspection_date' => now()->format('Y-m-d'),
    ]);
    
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'cancelled'
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('warning','Inspection schedule has been cancelled.');
  }

  public function test_admin_user_can_cancel_upcoming_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'inspection_date' => now()->addDays(4)->format('Y-m-d'),
    ]);
    
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'cancelled'
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('warning','Inspection schedule has been cancelled.');
  }

  public function test_staff_user_cannot_cancel_cancelled_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'status' => 'cancelled'
    ]);
    
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'cancelled'
    ]);

    $response->assertForbidden();
  }

  public function test_staff_user_cannot_cancel_missed_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'inspection_date' => now()->subDays(3)->format('Y-m-d'),
    ]);
    
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'cancelled'
    ]);

    $response->assertForbidden();
  }

  public function test_staff_user_cannot_cancel_done_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'status' => 'done'
    ]);
    
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'cancelled'
    ]);

    $response->assertForbidden();
  }

  public function test_staff_user_can_cancel_now_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'inspection_date' => now()->format('Y-m-d'),
    ]);
    
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'cancelled'
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('warning','Inspection schedule has been cancelled.');
  }

  public function test_staff_user_can_cancel_upcoming_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'inspection_date' => now()->addDays(4)->format('Y-m-d'),
    ]);
    
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'cancelled'
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('warning','Inspection schedule has been cancelled.');
  }

  public function test_admin_user_cannot_mark_as_done_cancelled_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'status' => 'cancelled'
    ]);
    
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'done'
    ]);

    $response->assertForbidden();
  }

  public function test_admin_user_cannot_mark_as_done_done_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'status' => 'done'
    ]);
    
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'done'
    ]);

    $response->assertForbidden();
  }

  public function test_admin_user_cannot_mark_as_done_missed_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'inspection_date' => now()->subDays(3)->format('Y-m-d'),
    ]);
    
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'done'
    ]);

    $response->assertForbidden();
  }

  public function test_admin_user_can_mark_as_done_now_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'inspection_date' => now()->format('Y-m-d'),
    ]);
    
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'done'
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success','Inspection schedule has been marked done.');
  }

  public function test_admin_user_can_mark_as_done_upcoming_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ]);
    
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'done'
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success','Inspection schedule has been marked done.');
  }

  public function test_staff_user_cannot_mark_as_done_cancelled_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'status' => 'cancelled'
    ]);
    
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'done'
    ]);

    $response->assertForbidden();
  }

  public function test_staff_user_cannot_mark_as_done_done_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'status' => 'done'
    ]);
    
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'done'
    ]);

    $response->assertForbidden();
  }

  public function test_staff_user_cannot_mark_as_done_missed_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'inspection_date' => now()->subDays(3)->format('Y-m-d'),
    ]);
    
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'done'
    ]);

    $response->assertForbidden();
  }

  public function test_staff_user_can_mark_as_done_now_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'inspection_date' => now()->format('Y-m-d'),
    ]);
    
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'done'
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success','Inspection schedule has been marked done.');
  }

  public function test_staff_user_can_mark_as_done_upcoming_inspection_schedule()
  {
    $schedule = InspectionSchedule::factory()->create([
      'inspection_date' => now()->addDays(3)->format('Y-m-d'),
    ]);
    
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);
    
    $response = $this->patch(route('inspection-schedules.update', $schedule), [
      'status' => 'done'
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success','Inspection schedule has been marked done.');
  }
}
