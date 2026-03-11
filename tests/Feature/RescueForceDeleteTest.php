<?php

namespace Tests\Feature;

use App\Models\Rescue;
use App\Models\User;
use App\Models\AdoptionApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RescueForceDeleteTest extends TestCase
{
  use RefreshDatabase;

  /**
    * Test: Guest users cannot access force delete
  */
  public function test_guest_cannot_force_delete_rescue()
  {
    $rescue = Rescue::factory()->create();

    $response = $this->delete(route('rescues.forceDelete', $rescue->id));

    $response->assertRedirect(route('login'));
    $this->assertNotNull(Rescue::withTrashed()->find($rescue->id));
  }

  /**
    * Test: Regular users cannot force delete rescues
  */
  public function test_regular_user_cannot_force_delete_rescue()
  {
    $user = User::factory()->create();
    $rescue = Rescue::factory()->create();

    $response = $this->actingAs($user)->delete(route('rescues.forceDelete', $rescue->id));

    $response->assertForbidden();
  }

  /**
    * Test: Admin can force delete rescue without applications
  */
  public function test_admin_can_force_delete_rescue_without_applications()
  {
    $admin = User::factory()->admin()->create();
    $rescue = Rescue::factory()->create(['name' => 'Test Rescue']);

    $response = $this->actingAs($admin)->delete(route('rescues.forceDelete', $rescue->id));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Rescue permanently deleted successfully.');
    $this->assertNull(Rescue::withTrashed()->find($rescue->id));
  }

  /**
    * Test: Staff can force delete rescue without applications
  */
  public function test_staff_can_force_delete_rescue_without_applications()
  {
    $staff = User::factory()->staff()->create();
    $rescue = Rescue::factory()->create(['name' => 'Test Rescue']);

    $response = $this->actingAs($staff)->delete(route('rescues.forceDelete', $rescue->id));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Rescue permanently deleted successfully.');
    $this->assertNull(Rescue::withTrashed()->find($rescue->id));
  }

  /**
    * Test: Cannot force delete rescue with pending application
  */
  public function test_cannot_force_delete_rescue_with_pending_application()
  {
    $admin = User::factory()->admin()->create();
    $rescue = Rescue::factory()->create();

    // Create pending application
    AdoptionApplication::factory()->create([
      'rescue_id' => $rescue->id,
      'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->delete(route('rescues.forceDelete', $rescue->id));

    $response->assertForbidden();
    $this->assertNotNull(Rescue::withTrashed()->find($rescue->id));
  }

  /**
    * Test: Cannot force delete rescue with under_review application
  */
  public function test_cannot_force_delete_rescue_with_under_review_application()
  {
    $admin = User::factory()->admin()->create();
    $rescue = Rescue::factory()->create();

    AdoptionApplication::factory()->create([
      'rescue_id' => $rescue->id,
      'status' => 'under_review',
    ]);

    $response = $this->actingAs($admin)->delete(route('rescues.forceDelete', $rescue->id));

    $response->assertForbidden();
    $this->assertNotNull(Rescue::withTrashed()->find($rescue->id));
  }

  /**
    * Test: Cannot force delete rescue with approved application
  */
  public function test_cannot_force_delete_rescue_with_approved_application()
  {
    $admin = User::factory()->admin()->create();
    $rescue = Rescue::factory()->create();

    AdoptionApplication::factory()->create([
      'rescue_id' => $rescue->id,
      'status' => 'approved',
    ]);

    $response = $this->actingAs($admin)->delete(route('rescues.forceDelete', $rescue->id));

    $response->assertForbidden();
    $this->assertNotNull(Rescue::withTrashed()->find($rescue->id));
  }

  /**
    * Test: Can force delete rescue with rejected application
  */
  public function test_can_force_delete_rescue_with_rejected_application()
  {
    $admin = User::factory()->admin()->create();
    $rescue = Rescue::factory()->create();
    $rescue->delete();

    // Rejected applications should not prevent deletion
    AdoptionApplication::factory()->create([
      'rescue_id' => $rescue->id,
      'status' => 'rejected',
    ]);

    $response = $this->actingAs($admin)->delete(route('rescues.forceDelete', $rescue->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertNull(Rescue::withTrashed()->find($rescue->id));
  }

  /**
    * Test: Can force delete rescue with cancelled application
  */
  public function test_can_force_delete_rescue_with_cancelled_application()
  {
    $admin = User::factory()->admin()->create();
    $rescue = Rescue::factory()->create();
    $rescue->delete();

    // Cancelled applications should not prevent deletion
    AdoptionApplication::factory()->create([
      'rescue_id' => $rescue->id,
      'status' => 'cancelled',
    ]);

    $response = $this->actingAs($admin)->delete(route('rescues.forceDelete', $rescue->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertNull(Rescue::withTrashed()->find($rescue->id));
  }

  /**
    * Test: Cannot force delete rescue with multiple active applications
  */
  public function test_cannot_force_delete_rescue_with_multiple_active_applications()
  {
    $admin = User::factory()->admin()->create();
    $rescue = Rescue::factory()->create();

    // Create 3 active applications
    AdoptionApplication::factory()->create([
      'rescue_id' => $rescue->id,
      'status' => 'pending',
    ]);
    AdoptionApplication::factory()->create([
      'rescue_id' => $rescue->id,
      'status' => 'under_review',
    ]);
    AdoptionApplication::factory()->create([
      'rescue_id' => $rescue->id,
      'status' => 'approved',
    ]);

    $response = $this->actingAs($admin)->delete(route('rescues.forceDelete', $rescue->id));

    $response->assertForbidden();
    $this->assertNotNull(Rescue::withTrashed()->find($rescue->id));
  }

  /**
    * Test: Can force delete rescue with mix of active and inactive applications
  */
  public function test_can_force_delete_rescue_with_only_inactive_applications()
  {
    $admin = User::factory()->admin()->create();
    $rescue = Rescue::factory()->create();

    // Create rejected and cancelled applications (inactive)
    AdoptionApplication::factory()->create([
      'rescue_id' => $rescue->id,
      'status' => 'rejected',
    ]);
    AdoptionApplication::factory()->create([
      'rescue_id' => $rescue->id,
      'status' => 'cancelled',
    ]);

    $response = $this->actingAs($admin)->delete(route('rescues.forceDelete', $rescue->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertNull(Rescue::withTrashed()->find($rescue->id));
  }

  /**
    * Test: Cannot force delete with one active and multiple inactive applications
  */
  public function test_cannot_force_delete_with_one_active_among_inactive_applications()
  {
    $admin = User::factory()->admin()->create();
    $rescue = Rescue::factory()->create();

    // Mix of active and inactive
    AdoptionApplication::factory()->create([
      'rescue_id' => $rescue->id,
      'status' => 'rejected',
    ]);
    AdoptionApplication::factory()->create([
      'rescue_id' => $rescue->id,
      'status' => 'pending', 
    ]);
    AdoptionApplication::factory()->create([
      'rescue_id' => $rescue->id,
      'status' => 'cancelled',
    ]);

    $response = $this->actingAs($admin)->delete(route('rescues.forceDelete', $rescue->id));

    $response->assertForbidden();
    $this->assertNotNull(Rescue::withTrashed()->find($rescue->id));
  }

  /**
    * Test: Force delete works on active (non-trashed) rescue without applications
  */
  public function test_can_force_delete_active_rescue_without_applications()
  {
    $admin = User::factory()->admin()->create();
    $rescue = Rescue::factory()->create();

    $response = $this->actingAs($admin)->delete(route('rescues.forceDelete', $rescue->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertNull(Rescue::withTrashed()->find($rescue->id));
  }

  /**
    * Test: Force delete handles non-existent rescue gracefully
  */
  public function test_force_delete_non_existent_rescue_returns_404()
  {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->delete(route('rescues.forceDelete', 99999));

    $response->assertNotFound();
  }

  /**
    * Test: Force delete redirects to trash view
  */
  public function test_force_delete_redirects_to_rescues_index()
  {
    $admin = User::factory()->admin()->create();
    $rescue = Rescue::factory()->create();

    $response = $this->actingAs($admin)->delete(route('rescues.forceDelete', $rescue->id));

    $response->assertRedirect(route('rescues.index'));
  }

  /**
    * Test: Force delete removes all associated data
  */
  public function test_force_delete_removes_rescue_completely_from_database()
  {
    $admin = User::factory()->admin()->create();
    $rescue = Rescue::factory()->create(['name' => 'Test Rescue']);
    $rescueId = $rescue->id;

    $this->actingAs($admin)->delete(route('rescues.forceDelete', $rescueId));

    // Verify complete removal
    $this->assertDatabaseMissing('rescues', ['id' => $rescueId]);
    $this->assertNull(Rescue::withTrashed()->find($rescueId));
  }
  /**
    * Test: Policy is called during force delete
  */
  public function test_force_delete_calls_authorization_policy()
  {
    $user = User::factory()->create();
    $rescue = Rescue::factory()->create();

    $response = $this->actingAs($user)->delete(route('rescues.forceDelete', $rescue->id));

    // Should be blocked by policy
    $response->assertForbidden();
    $this->assertNotNull(Rescue::withTrashed()->find($rescue->id));
  }

}