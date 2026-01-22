<?php

namespace Tests\Feature;

use App\Models\Rescue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RescueDestroyTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannnot_destroy_rescue_record()
  {
    $rescue = Rescue::factory()->create();

    $response = $this->delete(route('rescues.destroy', $rescue));
    $response->assertRedirect(route('login'));
    $this->assertDatabaseHas('rescues', [
      'id' => $rescue->id,
    ]);
  }

  public function test_regular_user_cannnot_destroy_rescue_record()
  {
    $rescue = Rescue::factory()->create();
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->delete(route('rescues.destroy', $rescue));
    $response->assertForbidden();

    $this->assertDatabaseHas('rescues', [
      'id' => $rescue->id,
    ]);
  }

  public function test_admin_user_can_destroy_rescue_record()
  {
    $rescue = Rescue::factory()->create();
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $response = $this->delete(route('rescues.destroy', $rescue));
    $response->assertRedirect();
    $response->assertSessionHas('warning', 'Rescue profile for '. $rescue->name. ' has been archived!');
  }

  public function test_staff_user_can_destroy_rescue_record()
  {
    $rescue = Rescue::factory()->create();
    $staff = User::factory()->staff()->create();
    $this->actingAs($staff);

    $response = $this->delete(route('rescues.destroy', $rescue));
    $response->assertRedirect();
    $response->assertSessionHas('warning', 'Rescue profile for '. $rescue->name. ' has been archived!');
  }

  public function test_destroying_nonexistent_rescue_record_returns_404()
  {
    $user = User::factory()->create();
    $this->actingAs($user);
        
    $response = $this->delete(route('rescues.destroy', 99999));
        
    $response->assertNotFound();
  }

  public function test_rescue_record_is_soft_deleted_when_destroyed()
  {
    $rescue = Rescue::factory()->create();
    $staff = User::factory()->staff()->create();

    $rescueId = $rescue->id;
    $this->actingAs($staff);

    $this->delete(route('rescues.destroy', $rescue));
    $this->assertSoftDeleted('rescues', ['id' => $rescueId]);
  }

  public function test_rescues_deleted_at_is_not_null_after_destroying_rescue_record()
  {
    $rescue = Rescue::factory()->create();
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->delete(route('rescues.destroy', $rescue));
    $this->assertNotNull(Rescue::withTrashed()->find($rescue->id)->deleted_at);
  }

  public function test_trashed_rescue_record_cannot_be_destroyed_again()
  {
    $rescue = Rescue::factory()->trashed()->create();
    $staff = User::factory()->staff()->create();
    $this->actingAs($staff);

    $response = $this->delete(route('rescues.destroy', $rescue));

    $response->assertForbidden();
  }
}
