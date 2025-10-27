<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\AdoptionApplication;
use App\Models\User;

class AdoptionApplicationRestoreTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannot_restore_application()
  {
    $application = AdoptionApplication::factory()->cancelled()->trashed()->create();

    $response = $this->patch(route('adoption_applications.restore', $application));

    $response->assertRedirect(route('login'));
  }

  public function test_restoring_nonexistent_applications_returns_404()
  {
    $user = User::factory()->create();
    $this->actingAs($user);
        
    $response = $this->patch(route('adoption_applications.restore', 99999));
        
    $response->assertNotFound();
  }

  public function test_regular_user_cannot_restore_application()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $application = AdoptionApplication::factory()->cancelled()->trashed()->create([
      'user_id' => $user2->id
    ]);

    $this->actingAs($user1);
    $response = $this->patch(route('adoption_applications.restore', $application));

    $response->assertForbidden();
  }

  public function test_donation_owner_cannot_restore_non_trashed_application()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->cancelled()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($user1);
    $response = $this->patch(route('adoption_applications.restore', $application));

    $response->assertForbidden();
  }

  public function test_donation_owner_can_restore_trashed_application()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->trashed()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($user1);
    $response = $this->from(route('users.myAdoptionApplications'))->patch(route('adoption_applications.restore', $application));

    $response->assertRedirect(route('users.myAdoptionApplications'));
    $response->assertSessionHas('success','Adoption application for '. $application->rescue->name. ' has been restored.');
    $this->assertDatabaseHas('adoption_applications', ['id' => $application->id]);
  }

  public function test_admin_user_cannot_restore_non_trashed_application()
  {
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->cancelled()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($admin);
    $response = $this->patch(route('adoption_applications.restore', $application));

    $response->assertForbidden();
  }

  public function test_admin_user_can_restore_trashed_application()
  {
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->trashed()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($admin);

    $response = $this->from(route('dashboard.adoptionApplications'))->patch(route('adoption_applications.restore', $application));

    $response->assertRedirect(route('dashboard.adoptionApplications'));
    $response->assertSessionHas('success','Adoption application for '. $application->rescue->name. ' has been restored.');
    $this->assertDatabaseHas('adoption_applications', ['id' => $application->id]);
  }

  public function test_staff_user_cannot_restore_non_trashed_application()
  {
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->cancelled()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($staff);
    $response = $this->patch(route('adoption_applications.restore', $application));

    $response->assertForbidden();
  }

  public function test_staff_user_can_restore_trashed_application()
  {
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->trashed()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($staff);

    $response = $this->from(route('dashboard.adoptionApplications'))->patch(route('adoption_applications.restore', $application));

    $response->assertRedirect(route('dashboard.adoptionApplications'));
    $response->assertSessionHas('success','Adoption application for '. $application->rescue->name. ' has been restored.');
    $this->assertDatabaseHas('adoption_applications', ['id' => $application->id]);
  }

  public function test_deleted_at_set_to_null_when_application_is_restored()
  {
    $user = User::factory()->create();
    
     $application = AdoptionApplication::factory()->trashed()->create([
      'user_id' => $user->id
    ]);
    
    // Verify application is trashed
    $this->assertNotNull($application->deleted_at);
    $this->assertTrue($application->trashed());
    
    $this->actingAs($user);

    $response = $this->from(route('users.myAdoptionApplications'))->patch(route('adoption_applications.restore', $application));

    $response->assertRedirect(route('users.myAdoptionApplications'));
    $response->assertSessionHas('success','Adoption application for '. $application->rescue->name. ' has been restored.');
    
    // Refresh the application from database
    $application->refresh();
    
    // Verify deleted_at is now null
    $this->assertNull($application->deleted_at);
    $this->assertFalse($application->trashed());
    
    // Verify application can be found without withTrashed()
    $this->assertNotNull(AdoptionApplication::find($application->id));
  }

  public function test_non_trashed_application_cannot_be_restored()
  {
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();
    $application = AdoptionApplication::factory()->cancelled()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($staff);
     $response = $this->patch(route('adoption_applications.restore', $application));

    $response->assertForbidden();
  }
}
