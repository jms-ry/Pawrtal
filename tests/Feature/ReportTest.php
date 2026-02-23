<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\ReportAlert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
  use RefreshDatabase;

  // Report Model Tests
  // -------------------------------------------------------------------------
  // Helpers
  // -------------------------------------------------------------------------

  private function makeReport(array $attributes = []): Report
  {
    return Report::factory()->make($attributes);
  }

  private function createReport(array $attributes = []): Report
  {
    return Report::factory()->create($attributes);
  }

  // -------------------------------------------------------------------------
  // isLostReport() / isFoundReport()
  // -------------------------------------------------------------------------

  public function test_is_lost_report_returns_true_when_type_is_lost(): void
  {
    $report = $this->makeReport(['type' => 'lost']);

    $this->assertTrue($report->isLostReport());
  }

  public function test_is_lost_report_returns_false_when_type_is_found(): void
  {
    $report = $this->makeReport(['type' => 'found']);

    $this->assertFalse($report->isLostReport());
  }

  public function test_is_found_report_returns_true_when_type_is_found(): void
  {
    $report = $this->makeReport(['type' => 'found']);

    $this->assertTrue($report->isFoundReport());
  }

  public function test_is_found_report_returns_false_when_type_is_lost(): void
  {
    $report = $this->makeReport(['type' => 'lost']);

    $this->assertFalse($report->isFoundReport());
  }

  // -------------------------------------------------------------------------
  // getTypeFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_type_formatted_returns_ucfirst_type_and_species_for_active_report(): void
  {
    $report = $this->makeReport(['type' => 'lost', 'species' => 'dog']);

    $this->assertSame('Lost Dog', $report->type_formatted);
  }

  public function test_type_formatted_lowercases_before_ucfirst(): void
  {
    $report = $this->makeReport(['type' => 'FOUND', 'species' => 'CAT']);

    $this->assertSame('Found Cat', $report->type_formatted);
  }

  public function test_type_formatted_appends_archived_label_for_soft_deleted_report(): void
  {
    $report = $this->createReport(['type' => 'lost', 'species' => 'dog']);
    $report->delete();

    $this->assertSame('Lost Dog (Archived)', $report->fresh()->type_formatted);
  }

  // -------------------------------------------------------------------------
  // getImageUrlAttribute()
  // -------------------------------------------------------------------------

  public function test_image_url_uses_storage_path_when_image_contains_reports_images_directory(): void
  {
    $report = $this->makeReport(['image' => 'reports_images/photo.jpg']);

    $this->assertSame(asset('storage/reports_images/photo.jpg'), $report->image_url);
  }

  public function test_image_url_uses_asset_path_when_image_does_not_contain_reports_images_directory(): void
  {
    $report = $this->makeReport(['image' => 'images/default.jpg']);

    $this->assertSame(asset('images/default.jpg'), $report->image_url);
  }

  // -------------------------------------------------------------------------
  // foundLastSeenLocation()
  // -------------------------------------------------------------------------

  public function test_found_last_seen_location_returns_last_seen_location_when_only_last_seen_is_set(): void
  {
    $report = $this->makeReport([
      'last_seen_location' => 'CEBU CITY',
      'found_location'     => null,
    ]);

    $this->assertSame('Cebu city', $report->found_last_seen_location_formatted);
  }

  public function test_found_last_seen_location_returns_found_location_when_only_found_is_set(): void
  {
    $report = $this->makeReport([
      'last_seen_location' => null,
      'found_location'     => 'MANDAUE CITY',
    ]);

    $this->assertSame('Mandaue city', $report->found_last_seen_location_formatted);
  }

  public function test_found_last_seen_location_returns_empty_string_when_both_are_null(): void
  {
    $report = $this->makeReport([
      'last_seen_location' => null,
      'found_location'     => null,
    ]);

        $this->assertSame('', $report->found_last_seen_location_formatted);
  }

  public function test_found_last_seen_location_returns_empty_string_when_both_are_set(): void
  {
    $report = $this->makeReport([
      'last_seen_location' => 'Cebu City',
      'found_location'     => 'Mandaue City',
    ]);

    $this->assertSame('', $report->found_last_seen_location_formatted);
  }

  // -------------------------------------------------------------------------
  // foundLastSeenDate()
  // -------------------------------------------------------------------------

  public function test_found_last_seen_date_returns_formatted_last_seen_date_when_only_last_seen_is_set(): void
  {
    $report = $this->makeReport([
      'last_seen_date' => '2025-01-15',
      'found_date'     => null,
    ]);

    $this->assertSame('Jan 15, 2025', $report->found_last_seen_date);
  }

  public function test_found_last_seen_date_returns_formatted_found_date_when_only_found_is_set(): void
  {
    $report = $this->makeReport([
      'last_seen_date' => null,
      'found_date'     => '2025-03-20',
    ]);

    $this->assertSame('Mar 20, 2025', $report->found_last_seen_date);
  }

  public function test_found_last_seen_date_returns_empty_string_when_both_are_null(): void
  {
    $report = $this->makeReport([
      'last_seen_date' => null,
      'found_date'     => null,
    ]);

     $this->assertSame('', $report->found_last_seen_date);
  }

  public function test_found_last_seen_date_returns_empty_string_when_both_are_set(): void
  {
    $report = $this->makeReport([
      'last_seen_date' => '2025-01-15',
      'found_date'     => '2025-03-20',
    ]);

    $this->assertSame('', $report->found_last_seen_date);
  }

  // -------------------------------------------------------------------------
  // getConditionFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_condition_formatted_returns_unknown_when_null(): void
  {
    $report = $this->makeReport(['condition' => null]);

    $this->assertSame('Unknown', $report->condition_formatted);
  }

  public function test_condition_formatted_returns_ucfirst_lowercased_value(): void
  {
    $report = $this->makeReport(['condition' => 'INJURED']);

    $this->assertSame('Injured', $report->condition_formatted);
  }

  // -------------------------------------------------------------------------
  // getTemporaryShelterFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_temporary_shelter_formatted_returns_unknown_when_null(): void
  {
    $report = $this->makeReport(['temporary_shelter' => null]);

    $this->assertSame('Unknown', $report->temporary_shelter_formatted);
  }

  public function test_temporary_shelter_formatted_returns_ucfirst_lowercased_value(): void
  {
    $report = $this->makeReport(['temporary_shelter' => 'WITH OWNER']);

    $this->assertSame('With owner', $report->temporary_shelter_formatted);
  }

  // -------------------------------------------------------------------------
  // distinctiveFeatures()
  // -------------------------------------------------------------------------

  public function test_distinctive_features_returns_none_when_null(): void
  {
    $report = $this->makeReport(['distinctive_features' => null]);

    $this->assertSame('None', $report->distinctive_features_formatted);
  }

  public function test_distinctive_features_adds_period_if_missing(): void
  {
    $report = $this->makeReport(['distinctive_features' => 'has a scar on left ear']);

    $this->assertStringEndsWith('.', $report->distinctive_features_formatted);
  }

  public function test_distinctive_features_capitalizes_each_sentence(): void
  {
    $report = $this->makeReport(['distinctive_features' => 'scar on ear.missing tail.']);

    $this->assertSame('Scar on ear. Missing tail.', $report->distinctive_features_formatted);
  }

  public function test_distinctive_features_lowercases_then_capitalizes_sentences(): void
  {
    $report = $this->makeReport(['distinctive_features' => 'SCAR ON EAR.MISSING TAIL.']);

    $this->assertSame('Scar on ear. Missing tail.', $report->distinctive_features_formatted);
  }

  // -------------------------------------------------------------------------
  // getAnimalNameFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_animal_name_formatted_returns_headline_cased_name_when_set(): void
  {
    $report = $this->makeReport(['animal_name' => 'buddy boy']);

    $this->assertSame('Buddy Boy', $report->animal_name_formatted);
  }

  public function test_animal_name_formatted_returns_unknown_when_null(): void
  {
    $report = $this->makeReport(['animal_name' => null]);

    $this->assertSame('Unknown', $report->animal_name_formatted);
  }

  // -------------------------------------------------------------------------
  // statusLabel()
  // -------------------------------------------------------------------------

  public function test_status_label_returns_not_yet_resolved_when_status_is_active(): void
  {
    $report = $this->makeReport(['status' => 'active']);

    $this->assertSame('Not yet resolved', $report->statusLabel());
  }

  public function test_status_label_returns_resolved_when_status_is_not_active(): void
  {
    $report = $this->makeReport(['status' => 'resolved']);

    $this->assertSame('Resolved', $report->statusLabel());
  }

  // -------------------------------------------------------------------------
  // isStillActive()
  // -------------------------------------------------------------------------

  public function test_is_still_active_returns_true_when_status_is_active(): void
  {
    $report = $this->makeReport(['status' => 'active']);

    $this->assertTrue($report->isStillActive());
  }

  public function test_is_still_active_returns_false_when_status_is_resolved(): void
  {
    $report = $this->makeReport(['status' => 'resolved']);

    $this->assertFalse($report->isStillActive());
  }

  // -------------------------------------------------------------------------
  // ownerFullName()
  // -------------------------------------------------------------------------

  public function test_owner_full_name_returns_ucfirst_lowercased_first_and_last_name(): void
  {
    $user   = User::factory()->create(['first_name' => 'john', 'last_name' => 'doe']);
    $report = $this->createReport(['user_id' => $user->id]);

    $this->assertSame('John Doe', $report->ownerFullName());
  }

  // -------------------------------------------------------------------------
  // ownedByLoggedUser()
  // -------------------------------------------------------------------------

  public function test_owned_by_logged_user_returns_true_when_authenticated_user_owns_the_report(): void
  {
    $user   = User::factory()->create();
    $report = $this->createReport(['user_id' => $user->id]);

    $this->actingAs($user);

    $this->assertTrue($report->ownedByLoggedUser());
  }

  public function test_owned_by_logged_user_returns_false_when_authenticated_user_does_not_own_the_report(): void
  {
    $owner  = User::factory()->create();
    $other  = User::factory()->create();
    $report = $this->createReport(['user_id' => $owner->id]);

    $this->actingAs($other);

    $this->assertFalse($report->ownedByLoggedUser());
  }

  public function test_owned_by_logged_user_returns_false_when_no_user_is_authenticated(): void
  {
    $report = $this->createReport();

    $this->assertFalse($report->ownedByLoggedUser());
  }

  // -------------------------------------------------------------------------
  // loggedUserIsAdminOrStaff()
  // -------------------------------------------------------------------------

  public function test_logged_user_is_admin_or_staff_returns_true_for_admin(): void
  {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $report = $this->createReport();

    $this->assertTrue($report->loggedUserIsAdminOrStaff());
  }

  public function test_logged_user_is_admin_or_staff_returns_true_for_staff(): void
  {
    $staff = User::factory()->create(['role' => 'staff']);
    $this->actingAs($staff);

    $report = $this->createReport();

    $this->assertTrue($report->loggedUserIsAdminOrStaff());
  }

  public function test_logged_user_is_admin_or_staff_returns_null_for_regular_user(): void
  {
    $user = User::factory()->create(['role' => 'regular_user']);
    $this->actingAs($user);

    $report = $this->createReport();

    $this->assertNull($report->loggedUserIsAdminOrStaff());
  }

  public function test_logged_user_is_admin_or_staff_returns_null_when_not_authenticated(): void
  {
    $report = $this->createReport();

    $this->assertNull($report->loggedUserIsAdminOrStaff());
  }

  // -------------------------------------------------------------------------
  // hasBeenAlertedBy()
  // -------------------------------------------------------------------------

  public function test_has_been_alerted_by_returns_true_when_user_has_an_alert_for_the_report(): void
  {
    $user   = User::factory()->create();
    $report = $this->createReport();

    ReportAlert::factory()->create([
      'report_id' => $report->id,
      'user_id'   => $user->id,
    ]);

    $this->assertTrue($report->hasBeenAlertedBy($user));
  }

  public function test_has_been_alerted_by_returns_false_when_user_has_no_alert_for_the_report(): void
  {
    $user   = User::factory()->create();
    $report = $this->createReport();

    $this->assertFalse($report->hasBeenAlertedBy($user));
  }

  public function test_has_been_alerted_by_returns_false_when_a_different_user_has_an_alert(): void
  {
    $user    = User::factory()->create();
    $other   = User::factory()->create();
    $report  = $this->createReport();

    ReportAlert::factory()->create([
      'report_id' => $report->id,
      'user_id'   => $other->id,
    ]);

    $this->assertFalse($report->hasBeenAlertedBy($user));
  }

  // -------------------------------------------------------------------------
  // scopeVisibleTo()
  // -------------------------------------------------------------------------

  public function test_scope_visible_to_includes_soft_deleted_reports_for_admin(): void
  {
    $admin  = User::factory()->create(['role' => 'admin']);
    $report = $this->createReport();
    $report->delete();

    $results = Report::visibleTo($admin)->get();

    $this->assertTrue($results->contains('id', $report->id));
  }

  public function test_scope_visible_to_includes_soft_deleted_reports_for_staff(): void
  {
    $staff  = User::factory()->create(['role' => 'staff']);
    $report = $this->createReport();
    $report->delete();

    $results = Report::visibleTo($staff)->get();

    $this->assertTrue($results->contains('id', $report->id));
  }

  public function test_scope_visible_to_shows_own_soft_deleted_reports_for_regular_user(): void
  {
    $user   = User::factory()->create(['role' => 'regular_user']);
    $report = $this->createReport(['user_id' => $user->id]);
    $report->delete();

    $results = Report::visibleTo($user)->get();

    $this->assertTrue($results->contains('id', $report->id));
  }

  public function test_scope_visible_to_hides_other_users_soft_deleted_reports_for_regular_user(): void
  {
    $user   = User::factory()->create(['role' => 'regular_user']);
    $owner  = User::factory()->create();
    $report = $this->createReport(['user_id' => $owner->id]);
    $report->delete();

    $results = Report::visibleTo($user)->get();

    $this->assertFalse($results->contains('id', $report->id));
  }

  public function test_scope_visible_to_shows_all_active_reports_when_user_is_null(): void
  {
    $report = $this->createReport();

    $results = Report::visibleTo(null)->get();

    $this->assertTrue($results->contains('id', $report->id));
  }

  public function test_scope_visible_to_excludes_soft_deleted_reports_when_user_is_null(): void
  {
    $report = $this->createReport();
    $report->delete();

    $results = Report::visibleTo(null)->get();

    $this->assertFalse($results->contains('id', $report->id));
  }
}