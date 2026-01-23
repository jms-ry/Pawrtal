<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use App\Notifications\ReportAlertNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StoreReportAlertControllerTest extends TestCase
{
  use RefreshDatabase;

  public function test_authenticated_user_can_alert_report_owner(): void
  {
    Notification::fake();

    $owner = User::factory()->create();
    $alerter = User::factory()->create();

    $report = Report::factory()->create([
      'user_id' => $owner->id,
      'type' => 'lost',
      'species' => 'Dog',
      'animal_name' => 'Max',
    ]);

    $response = $this->actingAs($alerter)->post(route('reports.alert', $report));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'The report owner has been notified!');

    Notification::assertSentTo(
      $owner,
      ReportAlertNotification::class,
      function ($notification) use ($report, $alerter) {
        $notificationData = $notification->toArray($report->user);
                
        return $notificationData['report_id'] === $report->id && $notificationData['alerter_id'] === $alerter->id;
      }
    );
  }

  public function test_unauthenticated_user_cannot_alert_report_owner(): void
  {
    Notification::fake();

    $owner = User::factory()->create();
    $report = Report::factory()->create(['user_id' => $owner->id]);

    $response = $this->post(route('reports.alert', $report));

    $response->assertRedirect('/login');
        
    Notification::assertNothingSent();
  }

  public function test_user_cannot_alert_their_own_report(): void
  {
    Notification::fake();

    $user = User::factory()->create();
    $report = Report::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->post(route('reports.alert', $report));

    $response->assertRedirect();
    $response->assertSessionHas('error', 'You cannot alert yourself.');

    Notification::assertNothingSent();
  }

  public function test_notification_contains_correct_data(): void
  {
    Notification::fake();

    $owner = User::factory()->create([
      'first_name' => 'John',
      'last_name' => 'Doe',
    ]);

      $alerter = User::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane@example.com',
        'contact_number' => '09123456789',
    ]);

    $report = Report::factory()->create([
      'user_id' => $owner->id,
      'type' => 'lost',
      'species' => 'Dog',
      'animal_name' => 'Max',
    ]);

    $this->actingAs($alerter)->post(route('reports.alert', $report));

    Notification::assertSentTo(
      $owner,
      ReportAlertNotification::class,
      function ($notification) use ($report, $alerter) {
        $data = $notification->toArray($report->user);
                
        return $data['report_id'] === $report->id &&
          $data['report_type'] === 'lost' &&
          $data['alerter_id'] === $alerter->id &&
          $data['alerter_name'] === 'Jane Smith' &&
          $data['alerter_email'] === 'jane@example.com' &&
          $data['alerter_contact_number'] === '09123456789' &&
        str_contains($data['message'], 'Jane Smith has information about your lost dog named Max report');
      }
    );
  }

  public function test_notification_message_for_found_report_without_name(): void
  {
    Notification::fake();

    $owner = User::factory()->create();
    $alerter = User::factory()->create([
      'first_name' => 'Jane',
      'last_name' => 'Smith',
    ]);

    $report = Report::factory()->create([
      'user_id' => $owner->id,
      'type' => 'found',
      'species' => 'Cat',
      'animal_name' => null,
    ]);

    $this->actingAs($alerter)->post(route('reports.alert', $report));

    Notification::assertSentTo(
      $owner,
      ReportAlertNotification::class,
      callback: function ($notification) use ($report) {
        $data = $notification->toArray($report->user);
                
        return str_contains($data['message'], 'found cat report') && !str_contains($data['message'], 'named');
      }
    );
  }

  public function test_species_is_lowercase_in_notification_message(): void
  {
    Notification::fake();

    $owner = User::factory()->create();
    $alerter = User::factory()->create([
      'first_name' => 'Jane',
      'last_name' => 'Smith',
    ]);

    $report = Report::factory()->create([
      'user_id' => $owner->id,
      'type' => 'found',
      'species' => 'DOG', // uppercase in database
    ]);

    $this->actingAs($alerter)->post(route('reports.alert', $report));

    Notification::assertSentTo(
      $owner,
      ReportAlertNotification::class,
      function ($notification) use ($report) {
        $data = $notification->toArray($report->user);
                
        return str_contains($data['message'], 'found dog report') && !str_contains($data['message'], 'DOG');
      }
    );
  }

  public function test_multiple_users_can_alert_same_report(): void
  {
    Notification::fake();

    $owner = User::factory()->create();
    $alerter1 = User::factory()->create();
    $alerter2 = User::factory()->create();

    $report = Report::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($alerter1)->post(route('reports.alert', $report));
    $this->actingAs($alerter2)->post(route('reports.alert', $report));

    Notification::assertSentTo($owner, ReportAlertNotification::class, 2);
  }
}