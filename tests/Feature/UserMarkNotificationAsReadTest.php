<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ReportArchivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Str;
use Tests\TestCase;

class UserMarkNotificationAsReadTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannot_mark_notification_as_read()
  {
    $user = User::factory()->create();
    $notification = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Test notification'],
    ]);

    $response = $this->postJson("users/notifications/{$notification->id}/mark-as-read");

    $response->assertStatus(401);
  }

  public function test_authenticated_user_cannot_mark_others_notification_as_read()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $notification = $user1->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Test notification'],
    ]);

    $this->actingAs($user2);

    $response = $this->postJson("users/notifications/{$notification->id}/mark-as-read");

    $response->assertForbidden();
  }

  public function test_authenticated_user_can_mark_their_notification_as_read()
  {
    $user = User::factory()->create();

    $notification = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Test notification'],
    ]);

    $this->actingAs($user);

    $response = $this->postJson("users/notifications/{$notification->id}/mark-as-read");
    $response->assertJson(['success' => true]);
    $response->assertStatus(200);
    $this->assertNotNull($notification->fresh()->read_at);
  }

  public function test_marking_nonexistent_notification_returns_404()
  {
    $user = User::factory()->create();

    $notification = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Test notification'],
    ]);

    $this->actingAs($user);

    $customID = fake()->uuid();

    $response = $this->postJson("users/notifications/{$customID}/mark-as-read");

   $response->assertStatus(404);
  }

  public function test_marking_already_read_notification_as_read_returns_400()
  {
    $user = User::factory()->create();

    $notification = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Test notification'],
      'read_at' => now()
    ]);

    $this->actingAs($user);

    $response = $this->postJson("users/notifications/{$notification->id}/mark-as-read");

   $response->assertStatus(400);
  }
}
