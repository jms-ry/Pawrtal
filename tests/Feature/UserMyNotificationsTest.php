<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ReportArchivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Str;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Support\Facades\DB;
class UserMyNotificationsTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannot_access_users_myreports()
  {
    $response = $this->get(route('users.myNotifications'));

    $response->assertRedirect(route('login'));
  }

  public function test_authenticated_user_can_access_users_myreports()
  {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('users.myNotifications'));
    $response->assertOk();
  }

  public function test_authenticated_user_can_only_view_their_own_notifications()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Create notifications for user1 directly in database
    $user1Notifications = collect(range(1, 3))->map(function ($i) use ($user1) {
      return DB::table('notifications')->insertGetId([
        'id' => \Illuminate\Support\Str::uuid(),
        'type' => 'App\\Notifications\\TestNotification',
        'notifiable_type' => User::class,
        'notifiable_id' => $user1->id,
        'data' => json_encode(['message' => "Notification {$i} for User 1"]),
        'read_at' => null,
        'created_at' => now(),
        'updated_at' => now(),
      ]);
    });

    // Create notifications for user2
    $user2Notifications = collect(range(1, 2))->map(function ($i) use ($user2) {
      return DB::table('notifications')->insertGetId([
        'id' => \Illuminate\Support\Str::uuid(),
        'type' => 'App\\Notifications\\TestNotification',
        'notifiable_type' => User::class,
        'notifiable_id' => $user2->id,
        'data' => json_encode(['message' => "Notification {$i} for User 2"]),
        'read_at' => null,
        'created_at' => now(),
        'updated_at' => now(),
      ]);
    });

    // Act as user1
    $this->actingAs($user1);

    $response = $this->get(route('users.myNotifications'));
    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page->component('User/MyNotifications')
        ->has('notifications.data', 3) // Only user1's 3 notifications
        ->where('notifications.data', function ($notifications) use ($user1Notifications, $user2Notifications) {
          $ids = collect($notifications)->pluck('id')->toArray();

          // Assert all user1's notifications are present
          foreach ($user1Notifications as $notificationId) {
            if (!in_array($notificationId, $ids)) {
              return false;
            }
          }

          // Assert none of user2's notifications are present
          foreach ($user2Notifications as $notificationId) {
            if (in_array($notificationId, $ids)) {
              return false;
            }
          }

        return true;
      })
    );
  }

  public function test_searching_notification_using_data_message_returns_results_case_insensitive()
  {
    $user = User::factory()->create();

    // Insert 3 notifications with the same message
    foreach (range(1, 3) as $i) {
      DB::table('notifications')->insert([
        'id' => \Illuminate\Support\Str::uuid(),
        'type' => 'App\\Notifications\\TestNotification',
        'notifiable_type' => User::class,
        'notifiable_id' => $user->id,
        'data' => json_encode([
          'message' => 'You have been assigned for a home inspection.'
        ]),
        'read_at' => null,
        'created_at' => now(),
        'updated_at' => now(),
      ]);
    }

    $this->actingAs($user);

    // Perform search (case-insensitive)
    $response = $this->get(route('users.myNotifications', [
      'search' => 'YOU HAVE BEEN ASSIGNED FOR A HOME INSPECTION'
    ]));

    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page->component('User/MyNotifications')
        ->has('notifications.data', 3)
      ->where('filters.search', 'YOU HAVE BEEN ASSIGNED FOR A HOME INSPECTION')
    );
  }

  public function test_search_with_no_matches_returns_empty_results()
  {
    $user = User::factory()->create();

    foreach (range(1, 3) as $i) {
      DB::table('notifications')->insert([
        'id' => \Illuminate\Support\Str::uuid(),
        'type' => 'App\\Notifications\\TestNotification',
        'notifiable_type' => User::class,
        'notifiable_id' => $user->id,
        'data' => json_encode([
          'message' => 'You have been assigned for a home inspection.'
        ]),
        'read_at' => null,
        'created_at' => now(),
        'updated_at' => now(),
      ]);
    }

    $this->actingAs($user);

    $response = $this->get(route('users.myNotifications', [
      'search' => 'non existent notification'
    ]));

    $response->assertStatus(200);

    // Verify the page loads with no results and correct filters
    $response->assertInertia(fn (Assert $page) =>
      $page->component('User/MyNotifications')
        ->has('notifications.data', 0)
      ->where('filters.search', 'non existent notification')
    );
  }

  public function test_search_filter_with_partial_message_match_returns_correct_results()
  {
    $user = User::factory()->create();

    foreach (range(1, 3) as $i) {
      DB::table('notifications')->insert([
        'id' => \Illuminate\Support\Str::uuid(),
        'type' => 'App\\Notifications\\TestNotification',
        'notifiable_type' => User::class,
        'notifiable_id' => $user->id,
        'data' => json_encode([
          'message' => 'You have been assigned for a home inspection.'
        ]),
        'read_at' => null,
        'created_at' => now(),
        'updated_at' => now(),
      ]);
    }

    $this->actingAs($user);

    $response = $this->get(route('users.myNotifications', [
      'search' => 'home inspection'
    ]));

    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page->component('User/MyNotifications')
        ->has('notifications.data', 3)
      ->where('filters.search', 'home inspection')
    );
  }

  public function test_unread_readAt_filter_only_returns_unread_notifications()
  {
    // Arrange
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create unread notifications
    $unreadNotification1 = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Your report 1 has been archived.'],
      'read_at' => null, // unread
    ]);

    $unreadNotification2 = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Your report 2 has been archived.'],
      'read_at' => null, // unread
    ]);

    // Create read notifications
    $readNotification = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Your report 3 has been archived.'],
      'read_at' => now(), 
    ]);

    // Act: Request with unread filter
    $response = $this->get(route('users.myNotifications', ['read_at' => 'unread']));

    // Assert: HTTP response is OK
    $response->assertStatus(200);

    // Assert: Inertia response structure and data
    $response->assertInertia(fn (Assert $page) =>
      $page->component('User/MyNotifications')
        ->has('notifications.data', 2) // Only 2 unread notifications
        ->where('filters.read_at', 'unread')
        ->where('notifications.data.0.read_at', null)
        ->where('notifications.data.1.read_at', null)
      ->where('notifications.data', function ($notifications) use ($unreadNotification1, $unreadNotification2, $readNotification) {
        $ids = collect($notifications)->pluck('id')->toArray();
                
        // Verify unread notifications are present
        return in_array($unreadNotification1->id, $ids)
          && in_array($unreadNotification2->id, $ids)
          // Verify read notification is NOT present
        && !in_array($readNotification->id, $ids);
      })
    );
  }

  public function test_read_readAt_filter_only_returns_read_notifications()
  {
    // Arrange
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create unread notifications
    $unreadNotification = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Your report 1 has been archived.'],
      'read_at' => null, // unread
    ]);

    // Create read notifications
    $readNotification1 = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Your report 2 has been archived.'],
      'read_at' => now()->subDays(1), 
    ]);

    $readNotification2 = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Your report 3 has been archived.'],
      'read_at' => now()->subDays(2), 
    ]);

    // Act: Request with read filter
    $response = $this->get(route('users.myNotifications', ['read_at' => 'read']));

    // Assert: HTTP response is OK
    $response->assertStatus(200);

    // Assert: Inertia response structure and data
    $response->assertInertia(fn (Assert $page) =>
      $page->component('User/MyNotifications')
        ->has('notifications.data', 2) // Only 2 read notifications
        ->where('filters.read_at', 'read')
        ->where('notifications.data', function ($notifications) use ($readNotification1, $readNotification2, $unreadNotification) {
          $ids = collect($notifications)->pluck('id')->toArray();
                  
          // Verify read notifications are present
          return in_array($readNotification1->id, $ids)
            && in_array($readNotification2->id, $ids)
            // Verify unread notification is NOT present
          && !in_array($unreadNotification->id, $ids);
        })
      ->where('notifications.data', function ($notifications) {
        // Verify all have non-null read_at
        return collect($notifications)->every(fn($notification) => 
          !is_null($notification['read_at'])
        );
      })
    );
  }

  public function test_oldest_sort_filter_sorts_notifications_in_ascending_order()
  {
   // Arrange
    $user = User::factory()->create();
    $this->actingAs($user);

    $notification1 = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Your report 1 has been archived.'],
      'read_at' => null, 
      'created_at' => now()
    ]);

    $notification2 = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Your report 2 has been archived.'],
      'read_at' => null, 
      'created_at' => now()->subDays(2)
    ]);

    $notification3 = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Your report 3 has been archived.'],
      'read_at' => null,
      'created_at' => now()->subDays(3) 
    ]);

    // Act: Request with read filter
    $response = $this->get(route('users.myNotifications', ['sort' => 'asc']));

    // Assert: HTTP response is OK
    $response->assertStatus(200);

    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyNotifications')
        ->has('notifications.data')
      ->where('notifications.data', function ($notifications) use ($notification1, $notification2, $notification3) {
        $dates = collect($notifications)->pluck('created_at')->map(fn($date) => substr($date, 0, 10))->toArray();

        $expectedOrder = [
          $notification3->created_at->toDateString(), // oldest
          $notification2->created_at->toDateString(),
          $notification1->created_at->toDateString(), // newest
        ];

        return $dates === $expectedOrder;
      })
    );
  }

  public function test_newest_sort_filter_sorts_notifications_in_desccending_order()
  {
    // Arrange
    $user = User::factory()->create();
    $this->actingAs($user);

    $notification1 = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Your report 1 has been archived.'],
      'read_at' => null, 
      'created_at' => now()
    ]);

    $notification2 = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Your report 2 has been archived.'],
      'read_at' => null, 
      'created_at' => now()->subDays(2)
    ]);

    $notification3 = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Your report 3 has been archived.'],
      'read_at' => null,
      'created_at' => now()->subDays(3) 
    ]);

    // Act: Request with read filter
    $response = $this->get(route('users.myNotifications', ['sort' => 'desc']));

    // Assert: HTTP response is OK
    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page
        ->component('User/MyNotifications')
        ->has('notifications.data')
      ->where('notifications.data', function ($notifications) use ($notification1, $notification2, $notification3) {
        $dates = collect($notifications)->pluck('created_at')->map(fn($date) => substr($date, 0, 10))->toArray();

        $expectedOrder = [
          $notification1->created_at->toDateString(), // newest
          $notification2->created_at->toDateString(),
          $notification3->created_at->toDateString(), // oldest
        ];

        return $dates === $expectedOrder;
      })
    );
  }

  public function test_multiple_filters_and_search_filter_works_together()
  {
    // Arrange
    $user = User::factory()->create();
    $this->actingAs($user);

    $notification1 = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Your report 1 has been archived.'],
      'read_at' => null, 
      'created_at' => now()
    ]);

    $notification2 = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Your report 2 has been archived.'],
      'read_at' => now(), 
      'created_at' => now()->subDays(2)
    ]);

    $notification3 = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Your report 3 has been archived.'],
      'read_at' => null,
      'created_at' => now()->subDays(3) 
    ]);

    // Act: Request with read filter
    $response = $this->get(route('users.myNotifications', [
      'sort' => 'desc',
      'read_at' => 'read',
      'search' => 'has been archived'
    ]));

    // Assert: HTTP response is OK
    $response->assertStatus(200);

    $response->assertInertia(fn ($page) =>
      $page->component('User/MyNotifications')
        ->where('filters.sort', 'desc')
        ->where('filters.read_at', 'read')
        ->where('filters.search', 'has been archived')
        ->has('notifications.data', 1)
        ->where('notifications.data.0.data.message', 'Your report 2 has been archived.')
      ->where('notifications.data.0.read_at', $notification2->read_at?->toJSON())
    );
  }

  public function test_search_and_filter_results_are_paginated_with_10_items_page()
  {
    // Arrange
    $user = User::factory()->create();
    $this->actingAs($user);

    for ($i = 1; $i <= 15; $i++) {
      $user->notifications()->create([
        'id' => Str::uuid()->toString(),
        'type' => ReportArchivedNotification::class,
        'data' => ['message' => "Notification number {$i} has been created."],
        'read_at' => now(),
        'created_at' => now()->subDays($i),
      ]);
    }

    $response = $this->get(route('users.myNotifications', [
      'page' => 1,
      'search' => 'has been created',
      'read_at' => 'read',
    ]));

    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) =>
      $page->component('User/MyNotifications')
        ->has('notifications.data', 10)
        ->where('filters.search', 'has been created')
      ->where('filters.read_at', 'read')
    );

  }

  public function test_query_string_parameters_are_preserved_in_pagination_links()
  {
    // Arrange
    $user = User::factory()->create();
    $this->actingAs($user);

    for ($i = 1; $i <= 15; $i++) {
      $user->notifications()->create([
        'id' => Str::uuid()->toString(),
        'type' => ReportArchivedNotification::class,
        'data' => ['message' => "Notification number {$i} has been created."],
        'read_at' => now(),
        'created_at' => now()->subDays($i),
      ]);
    }

    $response = $this->get(route('users.myNotifications', [
      'search' => 'created',
      'read_at' => 'read',
      'page' => 2,
    ]));

    $response->assertStatus(200);

    // Assert: Check that pagination links include preserved query params
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyNotifications')
        ->where('filters.search', 'created')
        ->where('filters.read_at', 'read')
        ->has('notifications.links') // just ensure 'links' exists
      ->where('notifications.links', function ($links) {
        // Convert to collection for easier iteration
        return collect($links)->every(function ($link) {
        // Skip "null" links (like "previous" or "next" on first/last page)
        if (empty($link['url'])) return true;

        return str_contains($link['url'], 'search=created') &&
          str_contains($link['url'], 'read_at=read');
        });
      })
    );
  }

  public function test_filters_are_returned_in_response()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $params = [
      'search' => 'created',
      'sort' => 'asc',
      'read_at' => 'unread',
    ];

    $response = $this->get(route('users.myNotifications', $params));

    $response->assertInertia(fn ($page) =>
      $page->component('User/MyNotifications')
        ->where('filters.search', 'created')
        ->where('filters.sort', 'asc')
      ->where('filters.read_at', 'unread')
    );
  }

  public function test_notifications_are_ordered_by_created_at_in_descending_order_by_default()
  {
    // Arrange
    $user = User::factory()->create();
    $this->actingAs($user);

    $notification1 = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Your report 1 has been archived.'],
      'read_at' => null, 
      'created_at' => now()
    ]);

    $notification2 = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Your report 2 has been archived.'],
      'read_at' => null, 
      'created_at' => now()->subDays(2)
    ]);

    $notification3 = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => 'Your report 3 has been archived.'],
      'read_at' => null,
      'created_at' => now()->subDays(3) 
    ]);

    // Act: Request with read filter
    $response = $this->get(route('users.myNotifications'));

    // Assert: HTTP response is OK
    $response->assertStatus(200);

    // Extract donations from Inertia props
    $props = $response->original->getData()['page']['props'];
    $notifications = $props['notifications']['data'] ?? [];

    $this->assertCount(3, $notifications);

    $dates = collect($notifications)->pluck('created_at')->map(fn($date) => substr($date, 0, 10))->toArray();

    $expectedOrder = [
      $notification1->created_at->toDateString(), // newest
      $notification2->created_at->toDateString(),
      $notification3->created_at->toDateString(), // oldest
    ];


    $this->assertSame($expectedOrder, $dates, 'Notifications should be ordered by created_at descending by default.');

  }

  public function test_empty_string_search_returns_all_available_donations()
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    for ($i = 1; $i <= 15; $i++) {
      $user->notifications()->create([
        'id' => Str::uuid()->toString(),
        'type' => ReportArchivedNotification::class,
        'data' => ['message' => "Notification number {$i} has been created."],
        'read_at' => now(),
        'created_at' => now()->subDays($i),
      ]);
    }
    
    $response = $this->get(route('users.myNotifications', [
      'search' => '',
    ]));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyNotifications')
        ->where('filters.search', null)
       ->has('notifications.data')
    );
  }

  public function test_search_handles_special_characters_safely()
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => "Notification number 1 for O'Malley has been created."],
      'read_at' => now(),
      'created_at' => now(),
    ]);
    
    $response = $this->get(route('users.myNotifications', [
      'search' => "O'Mal",
    ]));

    $response->assertInertia(fn ($page) =>
      $page->component('User/MyNotifications')
        ->has('notifications.data')
      ->where('notifications.data.0.data.message', "Notification number 1 for O'Malley has been created.")
    );
  }

  public function test_requesting_page_beyond_available_pages_returns_empty_results()
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    for ($i = 1; $i <= 15; $i++) {
      $user->notifications()->create([
        'id' => Str::uuid()->toString(),
        'type' => ReportArchivedNotification::class,
        'data' => ['message' => "Notification number {$i} has been created."],
        'read_at' => now(),
        'created_at' => now()->subDays($i),
      ]);
    }
    
    $response = $this->get(route('users.myNotifications', ['page' => 999]));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyNotifications')->has('notifications.data', 0)->etc()
    );
  }

  public function test_search_with_url_encoded_characters()
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => "Notification number 1 for Max & Ruby has been created."],
      'read_at' => now(),
      'created_at' => now(),
    ]);
    
    $response = $this->get(route('users.myNotifications', ['search' => 'Max & Ruby']));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyNotifications')->has('notifications.data', 1)
    );
  }

  public function test_last_page_with_filters_shows_remaining_items()
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    for ($i = 1; $i <= 15; $i++) {
      $user->notifications()->create([
        'id' => Str::uuid()->toString(),
        'type' => ReportArchivedNotification::class,
        'data' => ['message' => "Notification number {$i} has been created."],
        'read_at' => now(),
        'created_at' => now()->subDays($i),
      ]);
    }
    
    $response = $this->get(route('users.myNotifications', [
      'read_at' => 'read',
      'page' => 2
    ]));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyNotifications')->has('notifications.data', 5) 
    );
  }

  public function test_extremely_long_search_string_handles_gracefully()
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => "Notification number 1 for Max & Ruby has been created."],
      'read_at' => now(),
      'created_at' => now(),
    ]);
    
    $longString = str_repeat('a', 1000);
    
    $response = $this->get(route('users.myNotifications', ['search' => $longString]));
    
    $response->assertStatus(200);
  }

  public function test_search_with_sql_injection_attempt_is_handled_safely()
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => "Notification number 1 for Max & Ruby has been created."],
      'read_at' => now(),
      'created_at' => now(),
    ]);
    
    $response = $this->get(route('users.myNotifications', [
      'search' => "'; DROP TABLE notifications; --"
    ]));
    
    $response->assertStatus(200);
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyNotifications')->has('notifications.data', 0)
    );
    
    // Verify the table still exists
    $this->assertDatabaseHas('notifications', ['created_at' => now()]);
  }

  public function test_read_at_filter_is_case_insensitive()
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => "Notification number 1 for Max & Ruby has been created."],
      'read_at' => now(),
      'created_at' => now(),
    ]);
    
    $response = $this->get(route('users.myNotifications', ['read_at' => 'READ']));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyNotifications')
        ->where('filters.read_at', 'READ')
      ->has('notifications.data', 1) // Should return no results
    );
  }

  public function test_empty_search_with_other_filters_works_correctly()
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => "Notification number 1 for Max & Ruby has been created."],
      'read_at' => now(),
      'created_at' => now(),
    ]);
    
    $response = $this->get(route('users.myNotifications', [
      'search' => '',
      'read_at' => 'read'
    ]));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyNotifications')
        ->where('filters.search', null)
        ->where('filters.read_at', 'read')
      ->has('notifications.data', 1)
    );
  }

  public function test_invalid_sort_parameter_defaults_to_desc_order()
  { 
    $user = User::factory()->create();
    $this->actingAs($user);

    $notification1 = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => "Notification number 1 for Max & Ruby has been created."],
      'read_at' => now(),
      'created_at' => now(),
    ]);

     $notification2 = $user->notifications()->create([
      'id' => Str::uuid()->toString(),
      'type' => ReportArchivedNotification::class,
      'data' => ['message' => "Notification number 1 for Max & Ruby has been created."],
      'read_at' => now(),
      'created_at' => now()->addDay(),
    ]);
    
    // Invalid sort value should be ignored, defaulting to desc
    $response = $this->get(route('users.myNotifications', ['sort' => 'invalid']));
    
    $response->assertInertia(fn ($page) =>
      $page->component('User/MyNotifications')
        ->where('filters.sort', null) // Should be null for invalid values
      ->where('notifications.data.0.type', $notification1->type) // Newest first (desc)
    );
  }

  public function test_response_include_previousUrl_value()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $response = $this->from('/')->get(route('users.myNotifications'));
    $response->assertOk();

    $response->assertInertia(fn ($page) =>
      $page->component('User/MyNotifications')->where('previousUrl', url('/'))
    );
  }

  public function test_user_data_includes_id_role_and_fullName_for_authenticated_user()
  {
    $user = User::factory()->admin()->create([
      'first_name' => 'Freddie',
      'last_name' => 'Freeman'
    ]);

    $this->actingAs($user);

    $response = $this->get(route('users.myNotifications'));

    $response->assertInertia(fn ($page) =>
      $page->component('User/MyNotifications')
        ->has('user')
        ->where('user.id', $user->id)
        ->where('user.role', $user->role)
      ->where('user.fullName', $user->fullName())
    );
  }
}
