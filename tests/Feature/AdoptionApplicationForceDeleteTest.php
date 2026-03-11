<?php

namespace Tests\Feature;

use App\Models\AdoptionApplication;
use App\Models\User;
use App\Notifications\AdoptionApplicationForceDeleteNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdoptionApplicationForceDeleteTest extends TestCase
{
  use RefreshDatabase;
  public function test_guest_user_cannot_force_delete_adoption_application()
  {
    $application = AdoptionApplication::factory()->create();

    $response = $this->delete(route('adoption_applications.forceDelete', $application));

    $response->assertRedirect('login');
    $this->assertNotNull(AdoptionApplication::withTrashed()->find($application->id));
  }

  public function test_admin_user_cannot_force_delete_adoption_application()
  {
    $application = AdoptionApplication::factory()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $response = $this->delete(route('adoption_applications.forceDelete', $application));

    $response->assertForbidden();
    $this->assertNotNull(AdoptionApplication::withTrashed()->find($application->id));
  }
  
  public function test_staff_user_cannot_force_delete_adoption_application()
  {
    $application = AdoptionApplication::factory()->create();
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $response = $this->delete(route('adoption_applications.forceDelete', $application));

    $response->assertForbidden();
    $this->assertNotNull(AdoptionApplication::withTrashed()->find($application->id));
  }

  public function test_authenticated_user_can_force_delete_own_cancelled_adoption_application()
  {
    $user = User::factory()->create();

    $application = AdoptionApplication::factory()->cancelled()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $response = $this->delete(route('adoption_applications.forceDelete', $application));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Adoption application permanently deleted successfully.');
    $this->assertNull(AdoptionApplication::withTrashed()->find($application->id));
  }

  public function test_authenticated_user_can_force_delete_own_rejected_adoption_application()
  {
    $user = User::factory()->create();

    $application = AdoptionApplication::factory()->rejected()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $response = $this->delete(route('adoption_applications.forceDelete', $application));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Adoption application permanently deleted successfully.');
    $this->assertNull(AdoptionApplication::withTrashed()->find($application->id));
  }

  public function test_authenticated_user_cannot_force_delete_own_pending_adoption_application()
  {
    $user = User::factory()->create();

    $application = AdoptionApplication::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $response = $this->delete(route('adoption_applications.forceDelete', $application));

    $response->assertForbidden();
    $this->assertNotNull(AdoptionApplication::withTrashed()->find($application->id));
  }

  public function test_authenticated_user_cannot_force_delete_own_under_review_adoption_application()
  {
    $user = User::factory()->create();

    $application = AdoptionApplication::factory()->under_review()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $response = $this->delete(route('adoption_applications.forceDelete', $application));

    $response->assertForbidden();
    $this->assertNotNull(AdoptionApplication::withTrashed()->find($application->id));
  }

  public function test_authenticated_user_cannot_force_delete_own_approved_adoption_application()
  {
    $user = User::factory()->create();

    $application = AdoptionApplication::factory()->approved()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $response = $this->delete(route('adoption_applications.forceDelete', $application));

    $response->assertForbidden();
    $this->assertNotNull(AdoptionApplication::withTrashed()->find($application->id));
  }
  public function test_authenticated_user_cannot_force_delete_other_users_cancelled_adoption_application()
  {
    $user = User::factory()->create();
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->cancelled()->create(['user_id' => $user1->id]);

    $this->actingAs($user);

    $response = $this->delete(route('adoption_applications.forceDelete', $application));

    $response->assertForbidden();
    $this->assertNotNull(AdoptionApplication::withTrashed()->find($application->id));
  }

  public function test_authenticated_user_cannot_force_delete_other_users_rejected_adoption_application()
  {
    $user = User::factory()->create();
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->rejected()->create(['user_id' => $user1->id]);

    $this->actingAs($user);

    $response = $this->delete(route('adoption_applications.forceDelete', $application));

    $response->assertForbidden();
    $this->assertNotNull(AdoptionApplication::withTrashed()->find($application->id));
  }

  public function test_authenticated_user_cannot_force_delete_other_users_pending_adoption_application()
  {
    $user = User::factory()->create();
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create(['user_id' => $user1->id]);

    $this->actingAs($user);

    $response = $this->delete(route('adoption_applications.forceDelete', $application));

    $response->assertForbidden();
    $this->assertNotNull(AdoptionApplication::withTrashed()->find($application->id));
  }

  public function test_authenticated_user_cannot_force_delete_other_users_under_review_adoption_application()
  {
    $user = User::factory()->create();
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->under_review()->create(['user_id' => $user1->id]);

    $this->actingAs($user);

    $response = $this->delete(route('adoption_applications.forceDelete', $application));

    $response->assertForbidden();
    $this->assertNotNull(AdoptionApplication::withTrashed()->find($application->id));
  }

  public function test_authenticated_user_cannot_force_delete_other_users_approved_adoption_application()
  {
    $user = User::factory()->create();
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->approved()->create(['user_id' => $user1->id]);

    $this->actingAs($user);

    $response = $this->delete(route('adoption_applications.forceDelete', $application));

    $response->assertForbidden();
    $this->assertNotNull(AdoptionApplication::withTrashed()->find($application->id));
  }

  public function test_force_delete_non_existent_adoption_application_returns_404()
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('adoption_applications.forceDelete', 999));

    $response->assertNotFound();
  }

  public function test_force_delete_redirects_to_user_my_adoption_applications_page()
  {
    $user = User::factory()->create();
    $application = AdoptionApplication::factory()->cancelled()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('adoption_applications.forceDelete', $application));

    $response->assertRedirect(route('users.myAdoptionApplications'));
  }

  public function test_force_delete_removes_adoption_application_completely_from_database()
  {
    $user = User::factory()->create();
    $application = AdoptionApplication::factory()->cancelled()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('adoption_applications.forceDelete', $application));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Adoption application permanently deleted successfully.');
    $this->assertNull(AdoptionApplication::withTrashed()->find($application->id));
  }

  public function test_force_delete_calls_authorization_policy()
  {
    $user = User::factory()->create();
    $application = AdoptionApplication::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('adoption_applications.forceDelete', $application));

    $this->assertFalse($user->can('forceDelete', $application));
  }

  public function test_force_delete_notification_is_fired_after_a_successfull_force_delete()
  {
    Notification::fake();

    $user = User::factory()->create();
    $application = AdoptionApplication::factory()->cancelled()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('adoption_applications.forceDelete', $application));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Adoption application permanently deleted successfully.');

    Notification::assertSentTo(
      $user,
      AdoptionApplicationForceDeleteNotification::class
    );
  }
}
