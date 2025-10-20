<?php

namespace Tests\Feature;

use App\Models\Rescue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RescueRestoreTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannot_restore_recue_record()
  {
    $rescue = Rescue::factory()->trashed()->create();

    $response = $this->patch(route('rescues.restore', $rescue));

    $response->assertRedirect(route('login'));
  }

  public function test_regular_user_cannot_restore_recue_record()
  {
    $rescue = Rescue::factory()->trashed()->create();

    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->patch(route('rescues.restore', $rescue));
    $response->assertForbidden();
  }

  public function test_admin_user_can_restore_recue_record()
  {
    $rescue = Rescue::factory()->trashed()->create();

    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $response = $this->patch(route('rescues.restore', $rescue));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Rescue profile for '. $rescue->name. ' has been restored!');

    $this->assertDatabaseHas('rescues', ['id' => $rescue->id]);
  }

  public function test_staff_user_can_restore_recue_record()
  {
    $rescue = Rescue::factory()->trashed()->create();

    $staff = User::factory()->staff()->create();
    $this->actingAs($staff);

    $response = $this->patch(route('rescues.restore', $rescue));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Rescue profile for '. $rescue->name. ' has been restored!');

    $this->assertDatabaseHas('rescues', ['id' => $rescue->id]);
  }

  public function test_restoring_non_existent_rescue_record_returns_404()
  {
    $user = User::factory()->create();
    $this->actingAs($user);
        
    $response = $this->patch(route('rescues.restore', 99999));
        
    $response->assertNotFound();
  }

  public function test_rescue_record_is_restored_when_restoring()
  {
    $staff = User::factory()->staff()->create();
    $rescue = Rescue::factory()->trashed()->create();

    //Verify deleted_at is not null before restoring
    $this->assertNotNull($rescue->deleted_at);
    $this->assertTrue($rescue->trashed());

    $this->actingAs($staff);

    $response = $this->patch(route('rescues.restore', $rescue));
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Rescue profile for '. $rescue->name. ' has been restored!');

    // Refresh the rescue from database
    $rescue->refresh();

    // Verify deleted_at is now null
    $this->assertNull($rescue->deleted_at);
    $this->assertFalse($rescue->trashed());

    //Verify rescue can be found without withTrashed()
    $this->assertNotNull(Rescue::find($rescue->id));
  }

  public function test_non_trashed_rescue_record_cannot_be_restored()
  {
    $staff = User::factory()->staff()->create();
    $rescue = Rescue::factory()->create();

    $this->actingAs($staff);

    $response = $this->patch(route('rescues.restore', $rescue));
    $response->assertForbidden();
  }
}
