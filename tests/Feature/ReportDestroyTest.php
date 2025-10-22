<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReportDestroyTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannot_destroy_report()
  {
    $report = Report::factory()->lost()->create();

    $response = $this->delete(route('reports.destroy', $report));
    $response->assertRedirect(route('login'));
    $this->assertDatabaseHas('reports', ['id' => $report->id]);
  }

  public function test_regular_user_cannot_destroy_report()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user2->id
    ]);

    $this->actingAs($user1);
    $response = $this->delete(route('reports.destroy', $report));

    $response->assertForbidden();
    $this->assertDatabaseHas('reports', ['id' => $report->id]);
  }

  public function test_destroying_nonexistent_report_returns_404()
  {
    $user = User::factory()->create();
    $this->actingAs($user);
        
    $response = $this->delete(route('reports.destroy', 99999));
        
    $response->assertNotFound();
  }

  public function test_report_owner_cannot_destroy_active_report()
  {
    $user = User::factory()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->delete(route('reports.destroy', $report));

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Active reports cannot be archived.');
  }

  public function test_report_owner_can_destroy_resolved_report()
  {
    $user = User::factory()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user->id,
      'status' => 'resolved'
    ]);

    $this->actingAs($user);

    $response = $this->delete(route('reports.destroy', $report));

    $response->assertRedirect();
    $response->assertSessionHas('warning', 'Report has been archived!');
  }

  public function test_admin_user_can_destroy_resolved_report()
  {
    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user->id,
      'status' => 'resolved'
    ]);

    $this->actingAs($admin);

    $response = $this->delete(route('reports.destroy', $report));

    $response->assertRedirect();
    $response->assertSessionHas('warning', 'Report has been archived!');
  }

  public function test_admin_user_cannot_destroy_active_report()
  {
    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user->id,
    ]);

    $this->actingAs($admin);

    $response = $this->delete(route('reports.destroy', $report));

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Active reports cannot be archived.');
  }

  public function test_staff_user_can_destroy_resolved_report()
  {
    $user = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user->id,
      'status' => 'resolved'
    ]);

    $this->actingAs($staff);

    $response = $this->delete(route('reports.destroy', $report));

    $response->assertRedirect();
    $response->assertSessionHas('warning', 'Report has been archived!');
  }

  public function test_staff_user_cannot_destroy_active_report()
  {
    $user = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user->id,
    ]);

    $this->actingAs($staff);

    $response = $this->delete(route('reports.destroy', $report));

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Active reports cannot be archived.');
  }

  public function test_report_record_is_soft_deleted_when_destroyed()
  {
    $user = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user->id,
      'status' => 'resolved'
    ]);

    $this->actingAs($staff);

    $reportId = $report->id;

    $response = $this->delete(route('reports.destroy', $report));

    $response->assertRedirect();
    $response->assertSessionHas('warning', 'Report has been archived!');

    $this->assertSoftDeleted('reports', ['id' => $reportId]);
  }

  public function test_report_deleted_at_is_not_null_after_destroying()
  {
    $user = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user->id,
      'status' => 'resolved'
    ]);

    $this->actingAs($staff);

    $response = $this->delete(route('reports.destroy', $report));

    $response->assertRedirect();
    $response->assertSessionHas('warning', 'Report has been archived!');

    $this->assertNotNull(Report::withTrashed()->find($report->id)->deleted_at);
  }

  public function test_trashed_report_record_cannot_be_destroyed_again()
  {
    $user = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $report = Report::factory()->lost()->trashed()->create([
      'user_id' => $user->id,
      'status' => 'resolved'
    ]);

    $this->actingAs($staff);

    $response = $this->delete(route('reports.destroy', $report));

    $response->assertForbidden();
  }
}
