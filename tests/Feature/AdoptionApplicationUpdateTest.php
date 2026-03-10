<?php

namespace Tests\Feature;

use App\Models\AdoptionApplication;
use App\Models\Rescue;
use App\Models\User;
use App\Notifications\AdoptionApplicationApprovedNotification;
use App\Notifications\AdoptionApplicationRejectedNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


class AdoptionApplicationUpdateTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannot_update_adoption_application()
  {
    $application = AdoptionApplication::factory()->create();

    $response = $this->put(route('adoption-applications.update', $application), [
      'reason_for_adoption' => 'Hacked Application',
    ]);

    $response->assertRedirect(route('login'));
    $this->assertDatabaseMissing('adoption_applications', ['reason_for_adoption' => 'Hacked Application']);
  }

  public function test_admin_user_cannot_update_others_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->create(['user_id' => $user1->id]);

    $this->actingAs($admin);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'reason_for_adoption' => 'Hacked Application',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['reason_for_adoption' => 'Hacked Application']);
  }

  public function test_admin_user_cannot_cancel_others_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->create(['user_id' => $user1->id]);

    $this->actingAs($admin);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'cancelled',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['status' => 'cancelled']);
  }

  public function test_admin_user_cannot_approve_pending_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($admin);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'approved',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['status' => 'approved']);
  }

  public function test_admin_user_cannot_approve_approved_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->approved()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($admin);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'approved',
    ]);

    $response->assertForbidden();
  }

  public function test_admin_user_cannot_approve_rejected_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->rejected()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($admin);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'approved',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['status' => 'approved']);
  }

  public function test_admin_user_cannot_approve_cancelled_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->cancelled()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($admin);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'approved',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['status' => 'approved']);
  }

  public function test_admin_user_can_approve_under_review_adoption_application()
  {
    $rescue = Rescue::factory()->available()->create();
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->under_review()->create([
      'user_id' => $user1->id,
      'rescue_id' => $rescue->id
    ]);

    $this->actingAs($admin);
    
    $response = $this->from(route('dashboard.adoptionApplications'))->put(route('adoption-applications.update', $application), [
      'status' => 'approved',
      'review_date' => Carbon::now(),
      'review_notes' => 'This application is approved.',
      'reviewed_by' => $admin->fullName(),
      
    ]);

    $response->assertRedirect(route('dashboard.adoptionApplications'));
    $response->assertSessionHas('success','Adoption application for '. $application->rescue->name. ' has been approved.');
  }

  public function test_staff_user_cannot_update_others_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create(['user_id' => $user1->id]);

    $this->actingAs($staff);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'reason_for_adoption' => 'Hacked Application',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['reason_for_adoption' => 'Hacked Application']);
  }

  public function test_staff_user_cannot_cancel_others_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create(['user_id' => $user1->id]);

    $this->actingAs($staff);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'cancelled',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['status' => 'cancelled']);
  }

  public function test_staff_user_cannot_approve_pending_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($staff);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'approved',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['status' => 'approved']);
  }

  public function test_staff_user_cannot_approve_approved_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->approved()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($staff);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'approved',
    ]);

    $response->assertForbidden();
  }

  public function test_staff_user_cannot_approve_rejected_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->rejected()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($staff);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'approved',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['status' => 'approved']);
  }

  public function test_staff_user_cannot_approve_cancelled_adoption_application()
  {
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->cancelled()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($staff);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'approved',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['status' => 'approved']);
  }

  public function test_staff_user_can_approve_under_review_adoption_application()
  {
    $rescue = Rescue::factory()->available()->create();
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->under_review()->create([
      'user_id' => $user1->id,
      'rescue_id' => $rescue->id
    ]);

    $this->actingAs($staff);
    
    $response = $this->from(route('dashboard.adoptionApplications'))->put(route('adoption-applications.update', $application), [
      'status' => 'approved',
      'review_date' => Carbon::now(),
      'review_notes' => 'This application is approved.',
      'reviewed_by' => $staff->name,
    ]);

    $response->assertRedirect(route('dashboard.adoptionApplications'));
    $response->assertSessionHas('success','Adoption application for '. $application->rescue->name. ' has been approved.');
  }

  public function test_regular_user_cannot_update_others_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($user2);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'reason_for_adoption' => 'Hacked Application',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['reason_for_adoption' => 'Hacked Application']);
  }

  public function test_regular_user_cannot_update_its_under_review_adoption_application()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
      'status' => 'under_review',
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'reason_for_adoption' => 'Updated Application'
    ];
    
    $response = $this->put(route('adoption-applications.update', $application), $updatedData);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['reason_for_adoption' => 'Updated Application']);
  }

  public function test_regular_user_cannot_update_its_approved_adoption_application()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
      'status' => 'approved',
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'reason_for_adoption' => 'Updated Application'
    ];
    
    $response = $this->put(route('adoption-applications.update', $application), $updatedData);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['reason_for_adoption' => 'Updated Application']);
  }

  public function test_regular_user_cannot_update_its_rejected_adoption_application()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
      'status' => 'rejected',
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'reason_for_adoption' => 'Updated Application'
    ];
    
    $response = $this->put(route('adoption-applications.update', $application), $updatedData);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['reason_for_adoption' => 'Updated Application']);
  }

  public function test_regular_user_can_update_its_pending_adoption_application()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'reason_for_adoption' => 'Updated Application'
    ];
    
    $response = $this->from(route('users.myAdoptionApplications'))->put(route('adoption-applications.update', $application), $updatedData);

    $response->assertRedirect(route('users.myAdoptionApplications'));
    $response->assertSessionHas('info','Adoption application for '. $application->rescue->name. ' has been updated.');
  }

  public function test_regular_user_cannot_cancel_others_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($user2);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'cancelled',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['status' => 'cancelled']);
  }

  public function test_regular_user_cannot_reject_others_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($user2);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'rejected',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['status' => 'rejected']);
  }

  public function test_regular_user_cannot_approve_others_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($user2);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'approved',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['status' => 'approved']);
  }

  public function test_regular_user_cannot_reject_its_adoption_application()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'status' => 'rejected'
    ];
    
    $response = $this->put(route('adoption-applications.update', $application), $updatedData);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['status' => 'rejected']);
  }

  public function test_regular_user_cannot_approve_its_adoption_application()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'status' => 'approved'
    ];
    
    $response = $this->put(route('adoption-applications.update', $application), $updatedData);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['status' => 'approved']);
  }
  
  public function test_admin_user_cannot_reject_approved_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->approved()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($admin);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'rejected',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['status' => 'rejected']);
  }

  public function test_admin_user_cannot_reject_rejected_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->rejected()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($admin);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'rejected',
    ]);

    $response->assertForbidden();
  }

  public function test_admin_user_can_reject_under_review_adoption_application()
  {
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->under_review()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($admin);
    
    $response = $this->from(route('dashboard.adoptionApplications'))->put(route('adoption-applications.update', $application), [
      'status' => 'rejected',
      'review_date' => Carbon::now(),
      'review_notes' => 'This application is rejected.',
      'reviewed_by' => $admin->name,
      
    ]);

    $response->assertRedirect(route('dashboard.adoptionApplications'));
    $response->assertSessionHas('error','Adoption application for '. $application->rescue->name. ' has been rejected.');
  }

  public function test_admin_user_can_reject_pending_adoption_application()
  {
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->under_review()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($admin);
    
    $response = $this->from(route('dashboard.adoptionApplications'))->put(route('adoption-applications.update', $application), [
      'status' => 'rejected',
      'review_date' => Carbon::now(),
      'review_notes' => 'This application is rejected.',
      'reviewed_by' => $admin->name,
      
    ]);

    $response->assertRedirect(route('dashboard.adoptionApplications'));
    $response->assertSessionHas('error','Adoption application for '. $application->rescue->name. ' has been rejected.');
  }

  public function test_admin_user_cannot_reject_cancelled_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->cancelled()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($admin);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'rejected',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['status' => 'rejected']);
  }

  public function test_staff_user_cannot_reject_approved_adoption_application()
  {
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->approved()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($staff);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'rejected',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['status' => 'rejected']);
  }

  public function test_staff_user_cannot_reject_rejected_adoption_application()
  {
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->rejected()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($staff);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'rejected',
    ]);

    $response->assertForbidden();
  }
  public function test_staff_user_cannot_reject_cancelled_adoption_application()
  {
    
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->cancelled()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($staff);
    
    $response = $this->put(route('adoption-applications.update', $application), [
      'status' => 'rejected',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('adoption_applications', ['status' => 'rejected']);
  }
  public function test_staff_user_can_reject_under_review_adoption_application()
  {
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->under_review()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($staff);
    
    $response = $this->from(route('dashboard.adoptionApplications'))->put(route('adoption-applications.update', $application), [
      'status' => 'rejected',
      'review_date' => Carbon::now(),
      'review_notes' => 'This application is rejected.',
      'reviewed_by' => $staff->name,
    ]);

    $response->assertRedirect(route('dashboard.adoptionApplications'));
    $response->assertSessionHas('error','Adoption application for '. $application->rescue->name. ' has been rejected.');
  }
  public function test_staff_user_can_reject_pending_adoption_application()
  {
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($staff);
    
    $response = $this->from(route('dashboard.adoptionApplications'))->put(route('adoption-applications.update', $application), [
      'status' => 'rejected',
      'review_date' => Carbon::now(),
      'review_notes' => 'This application is rejected.',
      'reviewed_by' => $staff->name,
    ]);

    $response->assertRedirect(route('dashboard.adoptionApplications'));
    $response->assertSessionHas('error','Adoption application for '. $application->rescue->name. ' has been rejected.');
  }

  public function test_updating_nonexistent_application_returns_404()
  {
    $user1 = User::factory()->create();

    $this->actingAs($user1);

    $updatedData = [
      'status' => 'invalid status'
    ];
    
    $response = $this->put(route('adoption-applications.update', 9999), $updatedData);

    $response->assertNotFound();
  }
  public function test_updating_with_invalid_status_fails_validation()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'status' => 'invalid status'
    ];
    
    $response = $this->put(route('adoption-applications.update', $application), $updatedData);

    $response->assertSessionHasErrors('status');
  }

  public function test_updating_with_invalid_preferred_inspection_start_date_fails_validation()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'preferred_inspection_start_date' => 'not a date'
    ];
    
    $response = $this->put(route('adoption-applications.update', $application), $updatedData);

    $response->assertSessionHasErrors('preferred_inspection_start_date');
  }

  public function test_updating_with_preferred_inspection_start_date_that_is_in_the_past_fails_validation()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'preferred_inspection_start_date' => now()->subDays(11)->format('Y-m-d')
    ];
    
    $response = $this->put(route('adoption-applications.update', $application), $updatedData);

    $response->assertSessionHasErrors('preferred_inspection_start_date');
  }

  public function test_updating_with_invalid_preferred_inspection_end_date_fails_validation()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'preferred_inspection_end_date' => 'not a date'
    ];
    
    $response = $this->put(route('adoption-applications.update', $application), $updatedData);

    $response->assertSessionHasErrors('preferred_inspection_end_date');
  }

  public function test_updating_with_preferred_inspection_end_date_that_is_in_the_past_fails_validation()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'preferred_inspection_end_date' => now()->subDays(11)->format('Y-m-d')
    ];
    
    $response = $this->put(route('adoption-applications.update', $application), $updatedData);

    $response->assertSessionHasErrors('preferred_inspection_end_date');
  }

  public function test_updating_with_preferred_inspection_end_date_that_is_before_the_start_date_fails_validation()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($user1);

    $preferredStartDate = now()->addDays(11);

    $updatedData = [
      'preferred_inspection_start_date' => $preferredStartDate->format('Y-m-d'),
      'preferred_inspection_end_date' => $preferredStartDate->subDays(11)->format('Y-m-d')
    ];
    
    $response = $this->put(route('adoption-applications.update', $application), $updatedData);

    $response->assertSessionHasErrors('preferred_inspection_end_date');
  }

  public function test_invalid_valid_id_format_fails_validation()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user->id,
    ]);
    
    $this->actingAs($user);


    $validId = UploadedFile::fake()->create('validId.docx');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $updatedData = [
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments
    ];

    $response = $this->put(route('adoption-applications.update', $application), $updatedData);
    $response->assertSessionHasErrors('valid_id');
  }

  public function test_valid_id_exceed_max_size_limit_fails_validation()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user->id,
    ]);
    
    $this->actingAs($user);


    $validId = UploadedFile::fake()->create('validId.pdf', 10000);
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $updatedData = [
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments
    ];

    $response = $this->put(route('adoption-applications.update', $application), $updatedData);
    $response->assertSessionHasErrors('valid_id');
  }

  public function test_old_valid_is_deleted_when_new_valid_is_uploaded()
  {
    $user = User::factory()->create();

    $oldImage = UploadedFile::fake()->image('old_valid_id.jpg');

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user->id,
      'valid_id' => $oldImage->store('images/adoption_applications/valid_ids', 'public')
    ]);

    $this->actingAs($user);

    $newImage = UploadedFile::fake()->image('new_valid_id.jpg');

    $updatedData = [
      'valid_id' => $newImage
    ];

    $response = $this->put(route('adoption-applications.update', $application), $updatedData);
    $response->assertRedirect();
    $response->assertSessionHas('info','Adoption application for '. $application->rescue->name. ' has been updated.');

    $application->refresh();

    // Assert old valid is deleted
    Storage::disk('public')->assertMissing($oldImage);

    // Assert new valid exists
    $this->assertNotNull($application->valid_id);
    $this->assertNotEquals($oldImage, $application->valid_id);
    Storage::disk('public')->assertExists($application->valid_id);
  }

  public function test_valid_id_is_store_properly_in_the_database_after_update()
  {
    $user = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    $newImage = UploadedFile::fake()->image('new_valid_id.jpg');

    $updatedData = [
      'valid_id' => $newImage
    ];

    $response = $this->put(route('adoption-applications.update', $application), $updatedData);
    $response->assertRedirect();
    $response->assertSessionHas('info','Adoption application for '. $application->rescue->name. ' has been updated.');

    $application->refresh();

    Storage::disk('public')->assertExists($application->valid_id);

    // Assert database was updated (excluding the file object)
    $this->assertDatabaseHas('adoption_applications', [
      'id' => $application->id,
      'valid_id' => $application->valid_id, // actual stored path
    ]);
  }

  public function test_valid_id_is_unchanged_when_no_new_valid_id_uploaded()
  {
    $user = User::factory()->create();
    $newImage = UploadedFile::fake()->image('new_valid_id.jpg')->store('images/adoption_applications/valid_ids', 'public');

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user->id,
      'valid_id' => $newImage
    ]);

    $this->actingAs($user);

    $existingImagePath = $application->valid_id;

    Storage::disk('public')->assertExists($existingImagePath);

    $updatedData = [
      'reason_for_adoption' => 'updated reason'
    ];

    $response = $this->put(route('adoption-applications.update', $application), $updatedData);
    $response->assertRedirect();
    $response->assertSessionHas('info','Adoption application for '. $application->rescue->name. ' has been updated.');

    $application->refresh();

    $this->assertEquals($existingImagePath, $application->valid_id);

    $this->assertDatabaseHas('adoption_applications', [
      'id' => $application->id,
      'valid_id' => $existingImagePath
    ]);

    Storage::disk('public')->assertExists($existingImagePath);
  }

  public function test_new_supporting_documents_are_appended_to_existing_supporting_documents()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    $existingDocument1 = UploadedFile::fake()->create('image1.jpg');
    $existingDocument2 = UploadedFile::fake()->create('document.pdf');

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user->id,
      'supporting_documents' => [
        $existingDocument1->store('images/adoption_applications/supporting_documents', 'public'),
        $existingDocument2->store('images/adoption_applications/supporting_documents', 'public')
      ]
    ]);

    foreach ($application->supporting_documents as $path) {
      Storage::disk('public')->assertExists($path);
    }

    $this->actingAs($user);

    $newImage1 = UploadedFile::fake()->create('new1.jpg');
    $newImage2 = UploadedFile::fake()->create('new2.jpg');

    $updatedData = [
      'supporting_documents' => [$newImage1,$newImage2],
      'reason_for_adoption' => 'updated reason'
    ];

    $response = $this->put(route('adoption-applications.update', $application), $updatedData);
    $response->assertRedirect();
    $response->assertSessionHas('info','Adoption application for '. $application->rescue->name. ' has been updated.');

    $application->refresh();

    foreach ($application->supporting_documents as $path) {
      Storage::disk('public')->assertExists($path);
    }

    // Assert that the new images were appended (not replaced)
    $this->assertCount(4, $application->supporting_documents, 'Supporting documents should now have 4 files total.');

    // Assert that the existing image paths are still in the images array
    $this->assertTrue(in_array($existingDocument1->hashName('images/adoption_applications/supporting_documents'), $application->supporting_documents));
    $this->assertTrue(in_array($existingDocument2->hashName('images/adoption_applications/supporting_documents'), $application->supporting_documents));

    // Assert that the new image paths are also included
    $this->assertTrue(in_array($newImage1->hashName('images/adoption_applications/supporting_documents'), $application->supporting_documents));
    $this->assertTrue(in_array($newImage2->hashName('images/adoption_applications/supporting_documents'), $application->supporting_documents));

    // Assert database was updated with the merged images array
    $this->assertDatabaseHas('adoption_applications', [
      'id' => $application->id,
      'reason_for_adoption' => 'updated reason',
    ]);

    // Assert total number of stored files is 4
    $this->assertCount(4, Storage::disk('public')->allFiles('images/adoption_applications/supporting_documents'));
  }

  public function test_existing_supporting_documents_are_unchanged_when_no_new_supporting_documents_uploaded()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    $existingDocument1 = UploadedFile::fake()->create('image1.jpg');
    $existingDocument2 = UploadedFile::fake()->create('document.pdf');

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user->id,
      'supporting_documents' => [
        $existingDocument1->store('images/adoption_applications/supporting_documents', 'public'),
        $existingDocument2->store('images/adoption_applications/supporting_documents', 'public')
      ]
    ]);

    foreach ($application->supporting_documents as $path) {
      Storage::disk('public')->assertExists($path);
    }

    $existingImages = $application->supporting_documents;
    $this->actingAs($user);

    $updatedData = [
      //'supporting_documents' => [$newImage1,$newImage2],
      'reason_for_adoption' => 'updated reason'
    ];

    $response = $this->put(route('adoption-applications.update', $application), $updatedData);
    $response->assertRedirect();
    $response->assertSessionHas('info','Adoption application for '. $application->rescue->name. ' has been updated.');

    $application->refresh();

    $this->assertEquals($existingImages, $application->supporting_documents, 'Supporting documents should remain unchanged.');

    // Assert database was updated with the merged images array
    $this->assertDatabaseHas('adoption_applications', [
      'id' => $application->id,
      'reason_for_adoption' => 'updated reason',
    ]);

    foreach ($existingImages as $path) {
      Storage::disk('public')->assertExists($path);
    }

    // Assert total number of stored files is 4
    $this->assertCount(2, Storage::disk('public')->allFiles('images/adoption_applications/supporting_documents'));
  }

  public function test_invalid_file_format_of_one_of_new_uploaded_supporting_documents_fails_validation()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    $existingDocument1 = UploadedFile::fake()->create('image1.jpg');
    $existingDocument2 = UploadedFile::fake()->create('document.pdf');

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user->id,
      'supporting_documents' => [
        $existingDocument1->store('images/adoption_applications/supporting_documents', 'public'),
        $existingDocument2->store('images/adoption_applications/supporting_documents', 'public')
      ]
    ]);

    $this->actingAs($user);

    $newImage1 = UploadedFile::fake()->create('new1.csv');
    $newImage2 = UploadedFile::fake()->create('new2.jpg');

    $updatedData = [
      'supporting_documents' => [$newImage1,$newImage2],
      'reason_for_adoption' => 'updated reason'
    ];

    $response = $this->put(route('adoption-applications.update', $application), $updatedData);
    $response->assertSessionHasErrors('supporting_documents.0');
  }

  public function test_one_of_new_uploaded_supporting_documents_exceeds_max_file_size_limit_fails_validation()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    $existingDocument1 = UploadedFile::fake()->create('image1.jpg');
    $existingDocument2 = UploadedFile::fake()->create('document.pdf');

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user->id,
      'supporting_documents' => [
        $existingDocument1->store('images/adoption_applications/supporting_documents', 'public'),
        $existingDocument2->store('images/adoption_applications/supporting_documents', 'public')
      ]
    ]);

    $this->actingAs($user);

    $newImage1 = UploadedFile::fake()->create('new1.pdf', 10000);
    $newImage2 = UploadedFile::fake()->create('new2.jpg');

    $updatedData = [
      'supporting_documents' => [$newImage1,$newImage2],
      'reason_for_adoption' => 'updated reason'
    ];

    $response = $this->put(route('adoption-applications.update', $application), $updatedData);
    $response->assertSessionHasErrors('supporting_documents.0');
  }

  public function test_updating_with_nullable_fields_empty_succeeds()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'reason_for_adoption' => 'updated reason',
      'reviewed_by' => null,
      'review_date' => null,
      'review_notes' => null
    ];
    
    $response = $this->from(route('users.myAdoptionApplications'))->put(route('adoption-applications.update', $application), $updatedData);

    $response->assertRedirect(route('users.myAdoptionApplications'));
    $response->assertSessionHas('info','Adoption application for '. $application->rescue->name. ' has been updated.');

    $this->assertDatabaseHas('adoption_applications', [
      'id' => $application->id,
      'reason_for_adoption' => 'updated reason',
    ]);
  }

  public function test_updating_trashed_record_succeeds()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->trashed()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'reason_for_adoption' => 'updated reason',
      'reviewed_by' => null,
      'review_date' => null,
      'review_notes' => null
    ];
    
    $response = $this->from(route('users.myAdoptionApplications'))->put(route('adoption-applications.update', $application), $updatedData);

    $response->assertRedirect(route('users.myAdoptionApplications'));
    $response->assertSessionHas('info','Adoption application for '. $application->rescue->name. ' has been updated.');

    $this->assertDatabaseHas('adoption_applications', [
      'id' => $application->id,
      'reason_for_adoption' => 'updated reason',
    ]);
  }

  public function test_updating_with_very_long_reason_for_adoption_fails_validation()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'reason_for_adoption' => str_repeat('a', 11256)
    ];
    
    $response = $this->put(route('adoption-applications.update', $application), $updatedData);

    $response->assertSessionHasErrors('reason_for_adoption');
  }

  public function test_updating_valid_id_when_original_file_no_longer_exists_succeeds()
  {
    $user = User::factory()->create();
    $application = AdoptionApplication::factory()->create([
      'user_id' => $user->id,
      'valid_id' => 'images/adoption_applications/valid_ids/deleted_file.jpg' // doesn't exist
    ]);
    
    $this->actingAs($user);
    $newImage = UploadedFile::fake()->image('new.jpg');
    
    $response = $this->patch(route('adoption-applications.update', $application), [
      'valid_id' => $newImage
    ]);
    
    $response->assertRedirect();
    $application->refresh();
    Storage::disk('public')->assertExists($application->valid_id);
  }

  public function test_partial_update_of_non_file_fields_succeeds()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'reason_for_adoption' => 'Updated Application',
      'preferred_inspection_start_date' => now()->addDays(5)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(12)->format('Y-m-d'),
    ];
    
    $response = $this->from(route('users.myAdoptionApplications'))->put(route('adoption-applications.update', $application), $updatedData);

    $response->assertRedirect(route('users.myAdoptionApplications'));
    $response->assertSessionHas('info','Adoption application for '. $application->rescue->name. ' has been updated.');
  }
  public function test_cannot_change_user_id_through_update()
  {
    // Security: ensure user_id can't be changed
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $application = AdoptionApplication::factory()->create(['user_id' => $user1->id]);
    
    $this->actingAs($user1);
    
    $response = $this->patch(route('adoption-applications.update', $application), [
      'user_id' => $user2->id, // try to steal ownership
      'reason_for_adoption' => 'Updated'
    ]);
    
    $response->assertRedirect();
    $application->refresh();
    $this->assertEquals($application->user_id, $user1->id);
  }

  public function test_cannot_change_rescue_id_through_update()
  {
    // Security: ensure user_id can't be changed
    $user1 = User::factory()->create();

    $rescue1 = Rescue::factory()->create();
    $rescue2 = Rescue::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'rescue_id' => $rescue1->id,
      'user_id' => $user1->id
    ]);
    
    $this->actingAs($user1);
    
    $response = $this->patch(route('adoption-applications.update', $application), [
      'reason_for_adoption' => 'Updated',
      'rescue_id' => $rescue2->id
    ]);
    
    $response->assertRedirect();
    $application->refresh();
    $this->assertEquals($application->rescue_id, $rescue1->id);
  }

  public function test_regular_user_can_cancel_own_pending_application()
  {
    $user1 = User::factory()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'status' => 'cancelled'
    ];
    
    $response = $this->from(route('users.myAdoptionApplications'))->put(route('adoption-applications.update', $application), $updatedData);

    $response->assertRedirect(route('users.myAdoptionApplications'));
    $response->assertSessionHas('warning','Adoption application for '. $application->rescue->name. ' has been cancelled.');
  }

  public function test_updating_with_invalid_reviewed_date_format_fails()
  {
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->under_review()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($admin);
    
    $response = $this->from(route('users.myAdoptionApplications'))->put(route('adoption-applications.update', $application), [
      'status' => 'rejected',
      'review_date' => 'not a date',
      'review_notes' => 'This application is rejected.',
      'reviewed_by' => $admin->name,
      
    ]);

    $response->assertSessionHasErrors('review_date');
  }

  public function test_approving_application_updates_rescue_adoption_status_to_adopted()
  {
    $rescue = Rescue::factory()->available()->create();
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->under_review()->create([
      'user_id' => $user1->id,
      'rescue_id' => $rescue->id
    ]);

    $this->actingAs($admin);
    
    $response = $this->from(route('dashboard.adoptionApplications'))->put(route('adoption-applications.update', $application), [
      'status' => 'approved',
      'review_date' => Carbon::now(),
      'review_notes' => 'This application is approved.',
      'reviewed_by' => $admin->name,
      
    ]);

    $response->assertRedirect(route('dashboard.adoptionApplications'));
    $response->assertSessionHas('success','Adoption application for '. $application->rescue->name. ' has been approved.');

    //Check rescue adoption status was updated to adopted
    $this->assertDatabaseHas('rescues', [
      'id' => $rescue->id,
      'adoption_status' => 'adopted',
    ]);

    // Verify the rescue model is updated
    $rescue->refresh();
    $this->assertEquals('adopted', $rescue->adoption_status);

    // Verify the application status is approved
    $application->refresh();
    $this->assertEquals('approved', $application->status);
    $this->assertEquals('This application is approved.', $application->review_notes);
    $this->assertNotNull($application->review_date);
  }

  public function test_rejecting_application_does_not_change_rescue_adoption_status()
  {
    $rescue = Rescue::factory()->available()->create();
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->under_review()->create([
      'user_id' => $user1->id,
      'rescue_id' => $rescue->id
    ]);

    $this->actingAs($admin);
    
    $response = $this->from(route('dashboard.adoptionApplications'))
    ->put(route('adoption-applications.update', $application), [
      'status' => 'rejected',
      'review_date' => Carbon::now(),
      'review_notes' => 'Application does not meet requirements.',
      'reviewed_by' => $admin->name,
    ]);

    $response->assertRedirect(route('dashboard.adoptionApplications'));
    $response->assertSessionHas('error');

    // Check rescue adoption status remains available
    $this->assertDatabaseHas('rescues', [
      'id' => $rescue->id,
      'adoption_status' => 'available',
    ]);

    $rescue->refresh();
    $this->assertEquals('available', $rescue->adoption_status);
  }

  public function test_approving_application_auto_rejects_other_pending_applications()
  {
    $rescue = Rescue::factory()->create(['adoption_status' => 'available']);
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user3 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    // Create multiple applications for the same rescue
    $application1 = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
      'rescue_id' => $rescue->id,
      'status' => 'under_review',
    ]);

    $application2 = AdoptionApplication::factory()->create([
      'user_id' => $user2->id,
      'rescue_id' => $rescue->id,
      'status' => 'pending',
    ]);

    $application3 = AdoptionApplication::factory()->create([
      'user_id' => $user3->id,
      'rescue_id' => $rescue->id,
      'status' => 'under_review',
    ]);

    $this->actingAs($admin);
        
    // Approve application1
    $response = $this->put(route('adoption-applications.update', $application1), [
      'status' => 'approved',
      'review_date' => Carbon::now(),
      'review_notes' => 'This application is approved.',
      'reviewed_by' => $admin->fullName(),
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Check application1 is approved
    $this->assertDatabaseHas('adoption_applications', [
      'id' => $application1->id,
      'status' => 'approved',
    ]);

    // Check application2 is auto-rejected
    $this->assertDatabaseHas('adoption_applications', [
      'id' => $application2->id,
      'status' => 'rejected',
      'review_notes' => "Automatically rejected because another applicant was approved for {$rescue->name}.",
      'reviewed_by' => 'System',
    ]);

    // Check application3 is auto-rejected
    $this->assertDatabaseHas('adoption_applications', [
      'id' => $application3->id,
      'status' => 'rejected',
      'review_notes' => "Automatically rejected because another applicant was approved for {$rescue->name}.",
      'reviewed_by' => 'System',
    ]);

    // Check rescue is adopted
    $rescue->refresh();
    $this->assertEquals('adopted', $rescue->adoption_status);
  }

  /**
    * Test: Auto-reject only affects pending and under_review applications
  */
  public function test_auto_reject_only_affects_pending_and_under_review_applications()
  {
    $rescue = Rescue::factory()->create(['adoption_status' => 'available']);
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user3 = User::factory()->create();
    $user4 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    // Create applications with different statuses
    $approvedApp = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
      'rescue_id' => $rescue->id,
      'status' => 'under_review',
    ]);

    $pendingApp = AdoptionApplication::factory()->create([
      'user_id' => $user2->id,
      'rescue_id' => $rescue->id,
      'status' => 'pending',
    ]);

    $alreadyRejectedApp = AdoptionApplication::factory()->create([
      'user_id' => $user3->id,
      'rescue_id' => $rescue->id,
      'status' => 'rejected',
    ]);

    $cancelledApp = AdoptionApplication::factory()->create([
      'user_id' => $user4->id,
      'rescue_id' => $rescue->id,
      'status' => 'cancelled',
    ]);

    $this->actingAs($admin);
        
    // Approve approvedApp
    $this->put(route('adoption-applications.update', $approvedApp), [
      'status' => 'approved',
      'review_date' => Carbon::now(),
      'review_notes' => 'Approved.',
      'reviewed_by' => $admin->fullName(),
    ]);

    // Check pending was auto-rejected
    $pendingApp->refresh();
    $this->assertEquals('rejected', $pendingApp->status);
    $this->assertEquals('System', $pendingApp->reviewed_by);

    // Check already rejected was NOT touched
    $alreadyRejectedApp->refresh();
    $this->assertEquals('rejected', $alreadyRejectedApp->status);
    $this->assertNotEquals('System', $alreadyRejectedApp->reviewed_by);

    // Check cancelled was NOT touched
    $cancelledApp->refresh();
    $this->assertEquals('cancelled', $cancelledApp->status);
  }

  /**
    * Test: Cannot approve application if rescue already adopted
  */
  public function test_cannot_approve_application_if_rescue_already_adopted()
  {
    $rescue = Rescue::factory()->create(['adoption_status' => 'adopted']);
    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $application = AdoptionApplication::factory()->create([
      'user_id' => $user->id,
      'rescue_id' => $rescue->id,
      'status' => 'under_review',
    ]);

    $this->actingAs($admin);
        
    $response = $this->from(route('dashboard.adoptionApplications'))->put(route('adoption-applications.update', $application), [
      'status' => 'approved',
      'review_date' => Carbon::now(),
      'review_notes' => 'This application is approved.',
      'reviewed_by' => $admin->fullName(),
    ]);

    // Should redirect with error
    $response->assertRedirect(route('dashboard.adoptionApplications'));
    $response->assertSessionHas('error', 'This rescue has already been adopted.');

    // Application should not be approved
    $application->refresh();
    $this->assertNotEquals('approved', $application->status);
    $this->assertEquals('under_review', $application->status);
  }

  /**
    * Test: Auto-rejected applications have correct review data
  */
  public function test_auto_rejected_applications_have_correct_review_data()
  {
    $rescue = Rescue::factory()->create(['adoption_status' => 'available']);
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $approvedApp = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
      'rescue_id' => $rescue->id,
      'status' => 'under_review',
    ]);

    $pendingApp = AdoptionApplication::factory()->create([
      'user_id' => $user2->id,
      'rescue_id' => $rescue->id,
      'status' => 'pending',
    ]);

    $this->actingAs($admin);
        
    // Approve application
    $this->put(route('adoption-applications.update', $approvedApp), [
      'status' => 'approved',
      'review_date' => Carbon::now(),
      'review_notes' => 'Approved.',
      'reviewed_by' => $admin->fullName(),
    ]);

    $pendingApp->refresh();

    // Check auto-rejected application has correct data
    $this->assertEquals('rejected', $pendingApp->status);
    $this->assertEquals('System', $pendingApp->reviewed_by);
    $this->assertEquals(
      "Automatically rejected because another applicant was approved for {$rescue->name}.",
      $pendingApp->review_notes
    );
    $reviewDate = Carbon::parse($pendingApp->review_date);
    $this->assertTrue($reviewDate->isToday());
  }

  public function test_observer_sends_notifications_for_auto_rejected_applications()
  {
    Notification::fake();

    $rescue = Rescue::factory()->create(['adoption_status' => 'available']);
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user3 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $approvedApp = AdoptionApplication::factory()->create([
      'user_id' => $user1->id,
      'rescue_id' => $rescue->id,
      'status' => 'under_review',
    ]);

    $pendingApp = AdoptionApplication::factory()->create([
      'user_id' => $user2->id,
      'rescue_id' => $rescue->id,
      'status' => 'pending',
    ]);

    $underReviewApp = AdoptionApplication::factory()->create([
      'user_id' => $user3->id,
      'rescue_id' => $rescue->id,
      'status' => 'under_review',
    ]);

    $this->actingAs($admin);
        
    // Approve application
    $this->put(route('adoption-applications.update', $approvedApp), [
      'status' => 'approved',
      'review_date' => Carbon::now(),
      'review_notes' => 'Approved.',
      'reviewed_by' => $admin->fullName(),
    ]);

    // Refresh models to get updated data
    $approvedApp->refresh();
    $pendingApp->refresh();
    $underReviewApp->refresh();
    
    // Verify statuses were updated correctly
    $this->assertEquals('approved', $approvedApp->status);
    $this->assertEquals('rejected', $pendingApp->status);
    $this->assertEquals('rejected', $underReviewApp->status);

    // Assert notifications sent to auto-rejected users
    Notification::assertSentTo(
      $user2,
      AdoptionApplicationRejectedNotification::class
    );

    Notification::assertSentTo(
      $user3,
      AdoptionApplicationRejectedNotification::class
    );

    // Assert notification sent to approved user
    Notification::assertSentTo(
      $user1,
      AdoptionApplicationApprovedNotification::class
    );
  }
}
