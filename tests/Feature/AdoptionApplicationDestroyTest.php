<?php

namespace Tests\Feature;

use App\Models\AdoptionApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdoptionApplicationDestroyTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannot_destroy_application()
  {
    $application = AdoptionApplication::factory()->create();

    $response = $this->delete(route('adoption-applications.destroy', $application));

    $response->assertRedirect(route('login'));
  }

  public function test_regular_user_cannot_destroy_others_application()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($user2);
    
    $response = $this->delete(route('adoption-applications.destroy', $application));

    $response->assertForbidden();
  }

  public function test_destroying_nonexistent_application_returns_404()
  {
    $user1 = User::factory()->create();


    $this->actingAs($user1);
    
    $response = $this->delete(route('adoption-applications.destroy', 99999));

    $response->assertNotFound();
  }

  public function test_application_owner_cannot_destroy_pending_applications()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($user1);
    
    $response = $this->delete(route('adoption-applications.destroy', $application));
    $response->assertForbidden();
  }

  public function test_application_owner_cannot_destroy_under_review_applications()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->under_review()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($user1);
    
    $response = $this->delete(route('adoption-applications.destroy', $application));
    $response->assertForbidden();
  }

  public function test_application_owner_can_destroy_cancelled_applications()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
      'status' => 'cancelled'
    ]);

    $this->actingAs($user1);
    
    $response = $this->from(route('users.myAdoptionApplications'))->delete(route('adoption-applications.destroy', $application));
    $response->assertRedirect(route('users.myAdoptionApplications'));
    $response->assertSessionHas('warning','Adoption application for '. $application->rescue->name. ' has been archived.');
    
  }

  public function test_application_owner_can_destroy_approved_applications()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->approved()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($user1);
    
    $response = $this->from(route('users.myAdoptionApplications'))->delete(route('adoption-applications.destroy', $application));
    $response->assertRedirect(route('users.myAdoptionApplications'));
    $response->assertSessionHas('warning','Adoption application for '. $application->rescue->name. ' has been archived.');
    
  }

  public function test_application_owner_can_destroy_rejected_applications()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->rejected()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($user1);
    
    $response = $this->from(route('users.myAdoptionApplications'))->delete(route('adoption-applications.destroy', $application));
    $response->assertRedirect(route('users.myAdoptionApplications'));
    $response->assertSessionHas('warning','Adoption application for '. $application->rescue->name. ' has been archived.');
    
  }

  public function test_admin_user_cannot_destroy_pending_applications()
  {
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($admin);
    
    $response = $this->from(route('dashboard.adoptionApplications'))->delete(route('adoption-applications.destroy', $application));
    $response->assertForbidden();
  }

  public function test_admin_user_cannot_destroy_under_review_applications()
  {
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->under_review()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($admin);
    
    $response = $this->from(route('dashboard.adoptionApplications'))->delete(route('adoption-applications.destroy', $application));
    $response->assertForbidden();
  }

  public function test_admin_user_can_destroy_cancelled_applications()
  {
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->cancelled()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($admin);
    
    $response = $this->from(route('dashboard.adoptionApplications'))->delete(route('adoption-applications.destroy', $application));
    $response->assertRedirect(route('dashboard.adoptionApplications'));
    $response->assertSessionHas('warning','Adoption application for '. $application->rescue->name. ' has been archived.');
    
  }

  public function test_admin_user_can_destroy_approved_applications()
  {
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->approved()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($admin);
    
    $response = $this->from(route('dashboard.adoptionApplications'))->delete(route('adoption-applications.destroy', $application));
    $response->assertRedirect(route('dashboard.adoptionApplications'));
    $response->assertSessionHas('warning','Adoption application for '. $application->rescue->name. ' has been archived.');
    
  }

  public function test_admin_user_can_destroy_rejected_applications()
  {
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->rejected()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($admin);
    
    $response = $this->from(route('dashboard.adoptionApplications'))->delete(route('adoption-applications.destroy', $application));
    $response->assertRedirect(route('dashboard.adoptionApplications'));
    $response->assertSessionHas('warning','Adoption application for '. $application->rescue->name. ' has been archived.');
    
  }

  public function test_staff_user_cannot_destroy_pending_applications()
  {
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($staff);
    
    $response = $this->from(route('dashboard.adoptionApplications'))->delete(route('adoption-applications.destroy', $application));
    $response->assertForbidden();
  }

  public function test_staff_user_cannot_destroy_under_review_applications()
  {
    $user1 = User::factory()->create();
    $staff = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->under_review()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($staff);
    
    $response = $this->from(route('dashboard.adoptionApplications'))->delete(route('adoption-applications.destroy', $application));
    $response->assertForbidden();
  }

  public function test_staff_user_can_destroy_cancelled_applications()
  {
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->cancelled()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($staff);
    
    $response = $this->from(route('dashboard.adoptionApplications'))->delete(route('adoption-applications.destroy', $application));
    $response->assertRedirect(route('dashboard.adoptionApplications'));
    $response->assertSessionHas('warning','Adoption application for '. $application->rescue->name. ' has been archived.');
    
  }

  public function test_staff_user_can_destroy_approved_applications()
  {
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->approved()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($staff);
    
    $response = $this->from(route('dashboard.adoptionApplications'))->delete(route('adoption-applications.destroy', $application));
    $response->assertRedirect(route('dashboard.adoptionApplications'));
    $response->assertSessionHas('warning','Adoption application for '. $application->rescue->name. ' has been archived.');
    
  }

  public function test_staff_user_can_destroy_rejected_applications()
  {
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->rejected()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($staff);
    
    $response = $this->from(route('dashboard.adoptionApplications'))->delete(route('adoption-applications.destroy', $application));
    $response->assertRedirect(route('dashboard.adoptionApplications'));
    $response->assertSessionHas('warning','Adoption application for '. $application->rescue->name. ' has been archived.');
    
  }

  public function test_application_is_soft_deleted_when_destoryed()
  {
    $user = User::factory()->create();
    $application = AdoptionApplication::factory()->rejected()->create([
      'user_id' => $user->id,
    ]);

    $applicationId = $application->id;

    $this->actingAs($user);
    $response = $this->from(route('dashboard.adoptionApplications'))->delete(route('adoption-applications.destroy', $application));
    $response->assertRedirect(route('dashboard.adoptionApplications'));
    $response->assertSessionHas('warning','Adoption application for '. $application->rescue->name. ' has been archived.');

    // This checks that the record exists with a non-null deleted_at
    $this->assertSoftDeleted('adoption_applications', ['id' => $applicationId]);
  }

  public function test_trashed_application_cannot_be_destroyed_again()
  {
    $user = User::factory()->create();
    $application = AdoptionApplication::factory()->rejected()->trashed()->create([
      'user_id' => $user->id,
    ]);
    $this->actingAs($user);

   $response = $this->from(route('users.myAdoptionApplications'))->delete(route('adoption-applications.destroy', $application));
    $response->assertForbidden();
  }
}
