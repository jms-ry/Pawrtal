<?php

namespace Tests\Feature;

use App\Models\AdoptionApplication;
use App\Models\InspectionSchedule;
use App\Models\Rescue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdoptionApplicationTest extends TestCase
{
  use RefreshDatabase;

  // Adoption Application model tests
  // -------------------------------------------------------------------------
  // Helpers
  // -------------------------------------------------------------------------

  private function createAdoptionApplication(array $attributes = []): AdoptionApplication
  {
    return AdoptionApplication::factory()->create($attributes);
  }

  private function makeAdoptionApplication(array $attributes = []): AdoptionApplication
  {
    return AdoptionApplication::factory()->make($attributes);
  }

  // -------------------------------------------------------------------------
  // canArchive()
  // -------------------------------------------------------------------------

  public function test_can_archive_returns_true_when_status_is_cancelled(): void
  {
    $application = $this->createAdoptionApplication(['status' => 'cancelled']);

    $this->assertTrue($application->canArchive());
  }

  public function test_can_archive_returns_true_when_status_is_approved(): void
  {
    $application = $this->createAdoptionApplication(['status' => 'approved']);

    $this->assertTrue($application->canArchive());
  }

  public function test_can_archive_returns_true_when_status_is_rejected(): void
  {
    $application = $this->createAdoptionApplication(['status' => 'rejected']);

    $this->assertTrue($application->canArchive());
  }

  public function test_can_archive_returns_null_when_status_is_pending(): void
  {
    $application = $this->createAdoptionApplication(['status' => 'pending']);

    $this->assertNull($application->canArchive());
  }

  public function test_can_archive_returns_null_when_status_is_under_review(): void
  {
    $application = $this->createAdoptionApplication(['status' => 'under_review']);

    $this->assertNull($application->canArchive());
  }

  public function test_can_archive_returns_false_when_application_is_soft_deleted(): void
  {
    $application = $this->createAdoptionApplication(['status' => 'cancelled']);
    $application->delete();

    $this->assertFalse($application->fresh()->canArchive());
  }

  public function test_can_archive_returns_false_for_approved_status_when_soft_deleted(): void
  {
    $application = $this->createAdoptionApplication(['status' => 'approved']);
    $application->delete();

    $this->assertFalse($application->fresh()->canArchive());
  }

  // -------------------------------------------------------------------------
  // getArchivedAttribute()
  // -------------------------------------------------------------------------

  public function test_archived_returns_yes_when_application_is_soft_deleted(): void
  {
    $application = $this->createAdoptionApplication();
    $application->delete();

    $this->assertSame('Yes', $application->fresh()->archived);
  }

  public function test_archived_returns_no_when_application_is_not_soft_deleted(): void
  {
    $application = $this->createAdoptionApplication();

    $this->assertSame('No', $application->archived);
  }

  // -------------------------------------------------------------------------
  // getApplicantFullNameAttribute()
  // -------------------------------------------------------------------------

  public function test_applicant_full_name_returns_user_full_name_when_user_exists(): void
  {
    $user        = User::factory()->create(['first_name' => 'Jane', 'last_name' => 'Doe']);
    $application = $this->createAdoptionApplication(['user_id' => $user->id]);

    $this->assertSame($user->fullName(), $application->applicant_full_name);
  }

  public function test_applicant_full_name_returns_deleted_user_when_user_is_null(): void
  {
    $application = $this->makeAdoptionApplication(['user_id' => null]);

    $this->assertSame('Deleted User', $application->applicant_full_name);
  }

  // -------------------------------------------------------------------------
  // getRescueNameFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_rescue_name_formatted_returns_headline_cased_name_for_active_rescue(): void
  {
    $rescue      = Rescue::factory()->create(['name' => 'buddy boy']);
    $application = $this->createAdoptionApplication(['rescue_id' => $rescue->id]);

    $this->assertSame('Buddy Boy', $application->rescue_name_formatted);
  }

  public function test_rescue_name_formatted_appends_archived_label_for_soft_deleted_rescue(): void
  {
    $rescue      = Rescue::factory()->create(['name' => 'buddy boy']);
    $application = $this->createAdoptionApplication(['rescue_id' => $rescue->id]);
    $rescue->delete();

    $this->assertSame('Buddy Boy (Archived)', $application->fresh()->rescue_name_formatted);
  }

  public function test_rescue_name_formatted_returns_permanently_deleted_message_when_rescue_is_null(): void
  {
    $application = $this->makeAdoptionApplication(['rescue_id' => null]);

    $this->assertSame('(Rescue Profile Permanently Deleted)', $application->rescue_name_formatted);
  }

  // -------------------------------------------------------------------------
  // getStatusLabelAttribute()
  // -------------------------------------------------------------------------

  public function test_status_label_replaces_underscores_with_spaces_and_title_cases(): void
  {
    $application = $this->makeAdoptionApplication(['status' => 'under_review']);

    $this->assertSame('Under Review', $application->status_label);
  }

  public function test_status_label_formats_single_word_status_correctly(): void
  {
    $application = $this->makeAdoptionApplication(['status' => 'pending']);

    $this->assertSame('Pending', $application->status_label);
  }

  public function test_status_label_formats_approved_status_correctly(): void
  {
    $application = $this->makeAdoptionApplication(['status' => 'approved']);

    $this->assertSame('Approved', $application->status_label);
  }

  // -------------------------------------------------------------------------
  // Date formatting attributes
  // -------------------------------------------------------------------------

  public function test_application_date_formatted_returns_correctly_formatted_date(): void
  {
    $application = $this->createAdoptionApplication(['application_date' => '2025-04-10']);

    $this->assertSame('Apr 10, 2025', $application->application_date_formatted);
  }

  public function test_inspection_start_date_formatted_returns_correctly_formatted_date(): void
  {
    $application = $this->makeAdoptionApplication(['preferred_inspection_start_date' => '2025-05-01']);

    $this->assertSame('May 01, 2025', $application->inspection_start_date_formatted);
  }

  public function test_inspection_end_date_formatted_returns_correctly_formatted_date(): void
  {
    $application = $this->makeAdoptionApplication(['preferred_inspection_end_date' => '2025-05-07']);

    $this->assertSame('May 07, 2025', $application->inspection_end_date_formatted);
  }

  // -------------------------------------------------------------------------
  // getValidIdUrlAttribute()
  // -------------------------------------------------------------------------

  public function test_valid_id_url_uses_storage_path_when_valid_id_contains_valid_ids_directory(): void
  {
    $application = $this->makeAdoptionApplication(['valid_id' => 'valid_ids/id.jpg']);

    $this->assertSame(asset('storage/valid_ids/id.jpg'), $application->valid_id_url);
  }

  public function test_valid_id_url_uses_asset_path_when_valid_id_does_not_contain_valid_ids_directory(): void
  {
    $application = $this->makeAdoptionApplication(['valid_id' => 'images/id.jpg']);

    $this->assertSame(asset('images/id.jpg'), $application->valid_id_url);
  }

  // -------------------------------------------------------------------------
  // getSupportingDocumentsUrlAttribute()
  // -------------------------------------------------------------------------

  public function test_supporting_documents_url_returns_empty_array_when_documents_is_empty(): void
  {
    $application = $this->makeAdoptionApplication(['supporting_documents' => []]);

    $this->assertSame([], $application->supporting_documents_url);
  }

  public function test_supporting_documents_url_returns_empty_array_when_documents_is_null(): void
  {
    $application = $this->makeAdoptionApplication(['supporting_documents' => null]);

    $this->assertSame([], $application->supporting_documents_url);
  }

  public function test_supporting_documents_url_uses_storage_path_for_supporting_documents_directory(): void
  {
    $application = $this->makeAdoptionApplication(['supporting_documents' => ['supporting_documents/file.pdf']]);

    $this->assertSame([asset('storage/supporting_documents/file.pdf')], $application->supporting_documents_url);
  }

  public function test_supporting_documents_url_uses_asset_path_for_non_supporting_documents_directory(): void
  {
    $application = $this->makeAdoptionApplication(['supporting_documents' => ['documents/file.pdf']]);

    $this->assertSame([asset('documents/file.pdf')], $application->supporting_documents_url);
  }

  public function test_supporting_documents_url_handles_mixed_paths_correctly(): void
  {
    $application = $this->makeAdoptionApplication(['supporting_documents' => [
      'supporting_documents/file1.pdf',
      'documents/file2.pdf',
    ]]);

    $expected = [
      asset('storage/supporting_documents/file1.pdf'),
      asset('documents/file2.pdf'),
    ];

    $this->assertSame($expected, $application->supporting_documents_url);
  }

  // -------------------------------------------------------------------------
  // getLoggedUserIsAdminOrStaffAttribute()
  // -------------------------------------------------------------------------

  public function test_logged_user_is_admin_or_staff_returns_string_true_for_admin(): void
  {
    $admin       = User::factory()->create(['role' => 'admin']);
    $application = $this->createAdoptionApplication();

    $this->actingAs($admin);

    $this->assertSame('true', $application->logged_user_is_admin_or_staff);
  }

  public function test_logged_user_is_admin_or_staff_returns_string_true_for_staff(): void
  {
    $staff       = User::factory()->create(['role' => 'staff']);
    $application = $this->createAdoptionApplication();

    $this->actingAs($staff);

    $this->assertSame('true', $application->logged_user_is_admin_or_staff);
  }

  public function test_logged_user_is_admin_or_staff_returns_string_false_for_regular_user(): void
  {
    $user        = User::factory()->create(['role' => 'regular_user']);
    $application = $this->createAdoptionApplication();

    $this->actingAs($user);

    $this->assertSame('false', $application->logged_user_is_admin_or_staff);
  }

  public function test_logged_user_is_admin_or_staff_returns_string_false_when_not_authenticated(): void
  {
    $application = $this->createAdoptionApplication();

    $this->assertSame('false', $application->logged_user_is_admin_or_staff);
  }

  // -------------------------------------------------------------------------
  // Inspection schedule delegated attributes
  // -------------------------------------------------------------------------

  public function test_inspection_location_returns_null_when_no_inspection_schedule_exists(): void
  {
    $application = $this->createAdoptionApplication();

    $this->assertNull($application->inspection_location);
  }

  public function test_inspection_location_delegates_to_inspection_schedule(): void
  {
    $application = $this->createAdoptionApplication();
    InspectionSchedule::factory()->create([
      'application_id'    => $application->id,
      'inspection_location' => 'cebu city',
    ]);

    $this->assertSame('Cebu City', $application->fresh()->inspection_location);
  }

  public function test_inspector_name_returns_null_when_no_inspection_schedule_exists(): void
  {
    $application = $this->createAdoptionApplication();

    $this->assertNull($application->inspector_name);
  }

  public function test_inspection_date_returns_null_when_no_inspection_schedule_exists(): void
  {
    $application = $this->createAdoptionApplication();

    $this->assertNull($application->inspection_date);
  }

  public function test_inspection_date_delegates_to_inspection_schedule(): void
  {
    $application = $this->createAdoptionApplication();
    InspectionSchedule::factory()->create([
      'application_id'  => $application->id,
      'inspection_date' => '2025-06-20',
    ]);

    $this->assertSame('Jun 20, 2025', $application->fresh()->inspection_date);
  }
}