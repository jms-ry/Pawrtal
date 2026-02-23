<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ConversationIndexTest extends TestCase
{
  use RefreshDatabase;

  // -------------------------------------------------------------------------
  // Helpers
  // -------------------------------------------------------------------------

  private function createConversation(User $participant1, User $participant2, ?string $lastMessageAt = null): Conversation
  {
    return Conversation::factory()->create([
      'participant1_id' => $participant1->id,
      'participant2_id' => $participant2->id,
      'last_message_at' => $lastMessageAt ?? now(),
    ]);
  }

  private function createMessage(Conversation $conversation, User $sender, string $status = 'sent'): Message
  {
    return Message::factory()->create([
      'conversation_id' => $conversation->id,
      'sender_id'       => $sender->id,
      'status'          => $status,
    ]);
  }

  // -------------------------------------------------------------------------
  // Authentication
  // -------------------------------------------------------------------------

  public function test_guest_cannot_access_conversations_index(): void
  {
    $this->get(route('conversations.index'))->assertRedirect(route('login'));
  }

  // -------------------------------------------------------------------------
  // Successful response
  // -------------------------------------------------------------------------

  public function test_authenticated_user_can_access_conversations_index(): void
  {
    $user = User::factory()->create();

    $this->actingAs($user)
      ->get(route('conversations.index'))
    ->assertOk();
  }

  public function test_index_renders_the_correct_inertia_component(): void
  {
    $user = User::factory()->create();

    $this->actingAs($user)
      ->get(route('conversations.index'))
    ->assertInertia(fn (Assert $page) => $page->component('Conversation/Index'));
  }

  // -------------------------------------------------------------------------
  // Inertia props shape
  // -------------------------------------------------------------------------

  public function test_index_returns_required_inertia_props(): void
  {
    $user = User::factory()->create();

    $this->actingAs($user)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
        ->component('Conversation/Index')
        ->has('previousUrl')
        ->has('users')
        ->has('conversations')
      ->has('user')
    );
  }

  public function test_index_returns_authenticated_user_in_props(): void
  {
    $user = User::factory()->create();

    $this->actingAs($user)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
        ->component('Conversation/Index')
      ->where('user.id', $user->id)
    );
  }

  // -------------------------------------------------------------------------
  // Conversations — visibility
  // -------------------------------------------------------------------------

  public function test_user_sees_conversations_where_they_are_participant1(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $conversation = $this->createConversation($user, $other);

    $this->actingAs($user)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
      ->component('Conversation/Index')
        ->has('conversations', 1)
      ->where('conversations.0.id', $conversation->id)
    );
  }

  public function test_user_sees_conversations_where_they_are_participant2(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $conversation = $this->createConversation($other, $user);

    $this->actingAs($user)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
        ->component('Conversation/Index')
        ->has('conversations', 1)
      ->where('conversations.0.id', $conversation->id)
    );
  }

  public function test_user_does_not_see_conversations_they_are_not_part_of(): void
  {
    $user       = User::factory()->create();
    $otherA     = User::factory()->create();
    $otherB     = User::factory()->create();

    $this->createConversation($otherA, $otherB);

    $this->actingAs($user)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
        ->component('Conversation/Index')
      ->has('conversations', 0)
    );
  }

  public function test_user_with_no_conversations_sees_empty_conversations_list(): void
  {
    $user = User::factory()->create();

    $this->actingAs($user)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
        ->component('Conversation/Index')
      ->has('conversations', 0)
    );
  }

  // -------------------------------------------------------------------------
  // Conversations — ordering
  // -------------------------------------------------------------------------

  public function test_conversations_are_ordered_by_last_message_at_descending(): void
  {
    $user   = User::factory()->create();
    $other1 = User::factory()->create();
    $other2 = User::factory()->create();

    $older = $this->createConversation($user, $other1, now()->subDays(2)->toDateTimeString());
    $newer = $this->createConversation($user, $other2, now()->toDateTimeString());

    $this->actingAs($user)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
        ->component('Conversation/Index')
        ->where('conversations.0.id', $newer->id)
      ->where('conversations.1.id', $older->id)
    );
  }

  // -------------------------------------------------------------------------
  // Conversations — eager loaded relationships
  // -------------------------------------------------------------------------

  public function test_conversations_include_participant1_with_correct_fields(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $this->createConversation($user, $other);

    $this->actingAs($user)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
      ->component('Conversation/Index')
      ->has('conversations.0.participant1', fn (Assert $p) => $p
        ->hasAll(['id', 'first_name', 'last_name', 'email', 'role'])
        ->missingAll(['password', 'remember_token'])
      )
    );
  }

  public function test_conversations_include_participant2_with_correct_fields(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $this->createConversation($user, $other);

    $this->actingAs($user)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
      ->component('Conversation/Index')
      ->has('conversations.0.participant2', fn (Assert $p) => $p
        ->hasAll(['id', 'first_name', 'last_name', 'email', 'role'])
        ->missingAll(['password', 'remember_token'])
      )
    );
  }

  public function test_conversations_include_messages_ordered_by_created_at_ascending(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $conversation = $this->createConversation($user, $other);

    $first  = $this->createMessage($conversation, $user);
    $second = $this->createMessage($conversation, $other);

    $this->actingAs($user)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
      ->component('Conversation/Index')
      ->where('conversations.0.messages.0.id', $first->id)
      ->where('conversations.0.messages.1.id', $second->id)
    );
  }

  // -------------------------------------------------------------------------
  // Unread count
  // -------------------------------------------------------------------------

  public function test_unread_count_reflects_messages_not_sent_by_user_and_not_read(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $conversation = $this->createConversation($user, $other);

    // 2 unread messages from the other participant
    $this->createMessage($conversation, $other, 'sent');
    $this->createMessage($conversation, $other, 'delivered');

    // 1 already-read message from the other participant (should not count)
    $this->createMessage($conversation, $other, 'read');

    // 1 message sent by the user themselves (should not count)
    $this->createMessage($conversation, $user, 'sent');

    $this->actingAs($user)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
      ->component('Conversation/Index')
      ->where('conversations.0.unread', 2)
    );
  }

  public function test_unread_count_is_zero_when_all_messages_are_read(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $conversation = $this->createConversation($user, $other);

    $this->createMessage($conversation, $other, 'read');

    $this->actingAs($user)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
      ->component('Conversation/Index')
      ->where('conversations.0.unread', 0)
    );
  }

  public function test_unread_count_is_zero_when_there_are_no_messages(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $this->createConversation($user, $other);

    $this->actingAs($user)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
      ->component('Conversation/Index')
      ->where('conversations.0.unread', 0)
    );
  }

  public function test_own_sent_messages_are_not_counted_as_unread(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $conversation = $this->createConversation($user, $other);

    // User sends messages to the other — should never count as unread for themselves
    $this->createMessage($conversation, $user, 'sent');
    $this->createMessage($conversation, $user, 'delivered');

    $this->actingAs($user)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
      ->component('Conversation/Index')
      ->where('conversations.0.unread', 0)
    );
  }

  // -------------------------------------------------------------------------
  // Users without conversations
  // -------------------------------------------------------------------------

  public function test_users_prop_contains_users_without_a_conversation_with_current_user(): void
  {
    $user      = User::factory()->create();
    $noConvo   = User::factory()->create();

    $this->actingAs($user)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
        ->component('Conversation/Index')
        ->has('users', 1)
      ->where('users.0.id', $noConvo->id)
    );
  }

  public function test_users_prop_excludes_users_who_already_have_a_conversation_with_current_user(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $this->createConversation($user, $other);

    $this->actingAs($user)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
        ->component('Conversation/Index')
      ->has('users', 0)
    );
  }

  public function test_users_prop_excludes_the_authenticated_user_themselves(): void
  {
    $user = User::factory()->create();

    $this->actingAs($user)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
        ->component('Conversation/Index')
      ->where('users', fn ($users) => collect($users)->doesntContain('id', $user->id))
    );
  }

  public function test_users_prop_returns_correct_fields_only(): void
  {
    $user  = User::factory()->create();
    User::factory()->create();

    $this->actingAs($user)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
      ->component('Conversation/Index')
      ->has('users.0', fn (Assert $u) => $u
        ->hasAll(['id', 'first_name', 'last_name', 'email', 'role'])
        ->missingAll(['password', 'remember_token'])
      )
    );
  }

  // -------------------------------------------------------------------------
  // Roles — all roles can access
  // -------------------------------------------------------------------------

  public function test_admin_user_can_access_conversations_index(): void
  {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)->get(route('conversations.index'))->assertOk();
  }

  public function test_staff_user_can_access_conversations_index(): void
  {
    $user = User::factory()->staff()->create();

    $this->actingAs($user)->get(route('conversations.index'))->assertOk();
  }

  public function test_regular_user_can_access_conversations_index(): void
  {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('conversations.index'))->assertOk();
  }

  // -------------------------------------------------------------------------
  // Cross-role conversations
  // -------------------------------------------------------------------------

  public function test_admin_and_staff_can_have_a_conversation_visible_to_both(): void
  {
    $admin = User::factory()->create(['role' => 'admin']);
    $staff = User::factory()->create(['role' => 'staff']);

    $conversation = $this->createConversation($admin, $staff);

    $this->actingAs($admin)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
      ->where('conversations.0.id', $conversation->id)
    );

    $this->actingAs($staff)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
      ->where('conversations.0.id', $conversation->id)
    );
  }

  public function test_admin_and_regular_user_can_have_a_conversation_visible_to_both(): void
  {
    $admin       = User::factory()->create(['role' => 'admin']);
    $regularUser = User::factory()->create(['role' => 'regular_user']);

    $conversation = $this->createConversation($admin, $regularUser);

    $this->actingAs($admin)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
      ->where('conversations.0.id', $conversation->id)
    );

    $this->actingAs($regularUser)
      ->get(route('conversations.index'))
      ->assertInertia(fn (Assert $page) => $page
      ->where('conversations.0.id', $conversation->id)
    );
  }
}