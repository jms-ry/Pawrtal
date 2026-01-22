<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ReportArchivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Str;
use Tests\TestCase;
class UserMarkAllNotificationsAsReadTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannot_mark_notification_as_read()
  {
    $user = User::factory()->create();
    for ($i = 1; $i <= 15; $i++) {
      $user->notifications()->create([
        'id' => Str::uuid()->toString(),
        'type' => ReportArchivedNotification::class,
        'data' => ['message' => "Notification number {$i} has been created."],
        'created_at' => now(),
      ]);
    }

    $response = $this->postJson("users/notifications/mark-all-as-read");

    $response->assertStatus(401);
  }

  public function test_authenticated_user_cannot_mark_all_unread_notifications_of_others_as_read()
  {
    $otherUser = User::factory()->create();
    $authUser = User::factory()->create();

    // Create unread notifications for another user
    for ($i = 1; $i <= 15; $i++) {
      $otherUser->notifications()->create([
        'id' => Str::uuid()->toString(),
        'type' => ReportArchivedNotification::class,
        'data' => ['message' => "Notification number {$i} has been created."],
        'created_at' => now(),
      ]);
    }

    $this->actingAs($authUser);

    $response = $this->postJson("users/notifications/mark-all-as-read");

    $response->assertStatus(404);
    $response->assertJsonStructure(['message']); 

    // Confirm all notifications of the other user remain unread
    $otherUser->notifications->each(function ($notification) {
      $this->assertNull($notification->fresh()->read_at);
    });
  }

  public function test_authenticated_user_cannot_mark_all_their_own_read_notifications_as_read()
  {
    $user = User::factory()->create();

    // All notifications already read
    for ($i = 1; $i <= 15; $i++) {
      $user->notifications()->create([
        'id' => Str::uuid()->toString(),
        'type' => ReportArchivedNotification::class,
        'data' => ['message' => "Notification number {$i} has been created."],
        'created_at' => now(),
        'read_at' => now()->subDay(), // already read
      ]);
    }

    $this->actingAs($user);

    // Capture current read_at values for later comparison
    $before = $user->notifications->pluck('read_at');

    $response = $this->postJson("users/notifications/mark-all-as-read");

    $response->assertStatus(404);
    $response->assertJsonStructure(['message']); 

    // Ensure read_at values did not change
    $after = $user->fresh()->notifications->pluck('read_at');
    $this->assertEquals($before, $after, 'Read_at timestamps should remain unchanged');
  }

  public function test_authenticated_user_can_mark_all_their_own_unread_notifications_as_read()
  { 
    $user = User::factory()->create();

    for ($i = 1; $i <= 15; $i++) {
      $user->notifications()->create([
        'id' => Str::uuid()->toString(),
        'type' => ReportArchivedNotification::class,
        'data' => ['message' => "Notification number {$i} has been created."],
        'created_at' => now(),
      ]);
    }

    $this->actingAs($user);

    $response = $this->postJson("users/notifications/mark-all-as-read");
    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $notifications = $user->fresh()->notifications;

    foreach($notifications as $notification){
      $this->assertNotNull($notification->read_at);
    }

  }
}
