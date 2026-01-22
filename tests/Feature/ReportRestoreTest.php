<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Report;
use App\Models\User;

class ReportRestoreTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannot_restore_report()
  {
    $report = Report::factory()->lost()->create();

    $response = $this->patch(route('reports.restore', $report));
    $response->assertRedirect(route('login'));
  }

  public function test_restoring_nonexistent_report_returns_404()
  {
    $user = User::factory()->create();
    $this->actingAs($user);
        
    $response = $this->patch(route('reports.restore', 99999));
        
    $response->assertNotFound();
  }

  public function test_regular_user_cannot_restore_report()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user2->id
    ]);

    $this->actingAs($user1);
    $response = $this->patch(route('reports.restore', $report));

    $response->assertForbidden();
  }

  public function test_report_owner_cannot_restore_non_trashed_report()
  {
    $user = User::factory()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->patch(route('reports.restore', $report));

    $response->assertForbidden();
  }

  public function test_report_owner_can_restore_trashed_report()
  {
    $user = User::factory()->create();

    $report = Report::factory()->lost()->trashed()->create([
      'user_id' => $user->id,
      'status' => 'resolved'
    ]);

    $this->actingAs($user);

    $response = $this->patch(route('reports.restore', $report));

    $response->assertRedirect();
    $response->assertSessionHas('success',  'Report has been restored!');
  }

  public function test_admin_user_can_restore_trashed_report()
  {
    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $report = Report::factory()->lost()->trashed()->create([
      'user_id' => $user->id,
      'status' => 'resolved'
    ]);

    $this->actingAs($admin);

    $response = $this->patch(route('reports.restore', $report));

    $response->assertRedirect();
    $response->assertSessionHas('success',  'Report has been restored!');
  }

  public function test_admin_user_cannot_restore_non_trashed_report()
  {
    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user->id,
    ]);

    $this->actingAs($admin);

    $response = $this->patch(route('reports.restore', $report));

    $response->assertForbidden();
  }

  public function test_staff_user_can_restore_trashed_report()
  {
    $user = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $report = Report::factory()->lost()->trashed()->create([
      'user_id' => $user->id,
      'status' => 'resolved'
    ]);

    $this->actingAs($staff);

    $response = $this->patch(route('reports.restore', $report));

    $response->assertRedirect();
    $response->assertSessionHas('success',  'Report has been restored!');
  }

  public function test_staff_user_cannot_restore_non_trashed_report()
  {
    $user = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user->id,
    ]);

    $this->actingAs($staff);

    $response = $this->patch(route('reports.restore', $report));

    $response->assertForbidden();
  }

  public function test_report_record_is_restored_when_restoring()
  {
    $user = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $report = Report::factory()->lost()->trashed()->create([
      'user_id' => $user->id,
      'status' => 'resolved'
    ]);

    //Verify deleted_at is not null before restoring
    $this->assertNotNull($report->deleted_at);
    $this->assertTrue($report->trashed());

    $this->actingAs($staff);

    $response = $this->patch(route('reports.restore', $report));

    $response->assertRedirect();
    $response->assertSessionHas('success',  'Report has been restored!');

    // Refresh the report from database
    $report->refresh();

    // Verify deleted_at is now null
    $this->assertNull($report->deleted_at);
    $this->assertFalse($report->trashed());

    //Verify rescue can be found without withTrashed()
    $this->assertNotNull(Report::find($report->id));
  }

  public function test_non_trashed_report_cannot_be_restored()
  {
    $user = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user->id,
      'status' => 'resolved'
    ]);

    $this->actingAs($staff);

    $response = $this->patch(route('reports.restore', $report));
    $response->assertForbidden();
  }
}
