<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationMarkAsReadTest extends TestCase
{
  use RefreshDatabase;

  // -------------------------------------------------------------------------
  // Helpers
  // -------------------------------------------------------------------------

  private function markAsReadUrl(Conversation $conversation): string
  {
    return route('conversations.markAsRead', $conversation);
  }

  private function createConversation(User $participant1, User $participant2): Conversation
  {
    return Conversation::factory()->create([
      'participant1_id' => $participant1->id,
      'participant2_id' => $participant2->id,
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

  public function test_guest_cannot_mark_messages_as_read(): void
  {
    $user         = User::factory()->create();
    $other        = User::factory()->create();
    $conversation = $this->createConversation($user, $other);

    $this->put($this->markAsReadUrl($conversation))->assertRedirect(route('login'));
  }

  // -------------------------------------------------------------------------
  // Authorization
  // -------------------------------------------------------------------------

  public function test_participant1_can_mark_messages_as_read(): void
  {
    $user         = User::factory()->create();
    $other        = User::factory()->create();
    $conversation = $this->createConversation($user, $other);

    $this->createMessage($conversation, $other, 'sent');

    $this->actingAs($user)
      ->put($this->markAsReadUrl($conversation))
    ->assertOk();
  }

  public function test_participant2_can_mark_messages_as_read(): void
  {
    $user         = User::factory()->create();
    $other        = User::factory()->create();
    $conversation = $this->createConversation($user, $other);

    $this->createMessage($conversation, $user, 'sent');

    $this->actingAs($other)
      ->put($this->markAsReadUrl($conversation))
    ->assertOk();
  }

  public function test_non_participant_is_forbidden_from_marking_messages_as_read(): void
  {
    $user         = User::factory()->create();
    $other        = User::factory()->create();
    $outsider     = User::factory()->create();
    $conversation = $this->createConversation($user, $other);

    $this->actingAs($outsider)
      ->put($this->markAsReadUrl($conversation))
    ->assertForbidden();
  }

  // -------------------------------------------------------------------------
  // Response
  // -------------------------------------------------------------------------

  public function test_returns_json_response_with_success_true(): void
  {
    $user         = User::factory()->create();
    $other        = User::factory()->create();
    $conversation = $this->createConversation($user, $other);

    $this->actingAs($user)
      ->put($this->markAsReadUrl($conversation))
      ->assertOk()
    ->assertJson(['success' => true]);
  }

  public function test_response_is_json(): void
  {
    $user         = User::factory()->create();
    $other        = User::factory()->create();
    $conversation = $this->createConversation($user, $other);

    $this->actingAs($user)
      ->put($this->markAsReadUrl($conversation))
    ->assertHeader('Content-Type', 'application/json');
  }

  // -------------------------------------------------------------------------
  // Marking messages as read
  // -------------------------------------------------------------------------

  public function test_sent_messages_from_other_participant_are_marked_as_read(): void
  {
    $user         = User::factory()->create();
    $other        = User::factory()->create();
    $conversation = $this->createConversation($user, $other);

    $message = $this->createMessage($conversation, $other, 'sent');

    $this->actingAs($user)->put($this->markAsReadUrl($conversation));

    $this->assertDatabaseHas('messages', [
      'id'     => $message->id,
      'status' => 'read',
    ]);
  }

  public function test_delivered_messages_from_other_participant_are_marked_as_read(): void
  {
    $user         = User::factory()->create();
    $other        = User::factory()->create();
    $conversation = $this->createConversation($user, $other);

    $message = $this->createMessage($conversation, $other, 'delivered');

    $this->actingAs($user)->put($this->markAsReadUrl($conversation));

    $this->assertDatabaseHas('messages', [
      'id'     => $message->id,
      'status' => 'read',
    ]);
  }

  public function test_multiple_unread_messages_from_other_participant_are_all_marked_as_read(): void
  {
    $user         = User::factory()->create();
    $other        = User::factory()->create();
    $conversation = $this->createConversation($user, $other);

    $message1 = $this->createMessage($conversation, $other, 'sent');
    $message2 = $this->createMessage($conversation, $other, 'sent');
    $message3 = $this->createMessage($conversation, $other, 'delivered');

    $this->actingAs($user)->put($this->markAsReadUrl($conversation));

    foreach ([$message1, $message2, $message3] as $message) {
      $this->assertDatabaseHas('messages', [
        'id'     => $message->id,
        'status' => 'read',
      ]);
    }
  }

  public function test_already_read_messages_from_other_participant_are_not_affected(): void
  {
    $user         = User::factory()->create();
    $other        = User::factory()->create();
    $conversation = $this->createConversation($user, $other);

    $message = $this->createMessage($conversation, $other, 'read');

    $this->actingAs($user)->put($this->markAsReadUrl($conversation));

    // Still read — no unnecessary update
    $this->assertDatabaseHas('messages', [
      'id'     => $message->id,
      'status' => 'read',
    ]);
  }

  // -------------------------------------------------------------------------
  // Own messages are never affected
  // -------------------------------------------------------------------------

  public function test_own_sent_messages_are_not_marked_as_read(): void
  {
    $user         = User::factory()->create();
    $other        = User::factory()->create();
    $conversation = $this->createConversation($user, $other);

    $ownMessage = $this->createMessage($conversation, $user, 'sent');

    $this->actingAs($user)->put($this->markAsReadUrl($conversation));

    $this->assertDatabaseHas('messages', [
      'id'     => $ownMessage->id,
      'status' => 'sent',
    ]);
  }

  public function test_own_delivered_messages_are_not_marked_as_read(): void
  {
    $user         = User::factory()->create();
    $other        = User::factory()->create();
    $conversation = $this->createConversation($user, $other);

    $ownMessage = $this->createMessage($conversation, $user, 'delivered');

    $this->actingAs($user)->put($this->markAsReadUrl($conversation));

    $this->assertDatabaseHas('messages', [
      'id'     => $ownMessage->id,
      'status' => 'delivered',
    ]);
  }

  // -------------------------------------------------------------------------
  // Mixed messages — only the right ones are updated
  // -------------------------------------------------------------------------

  public function test_only_other_participants_unread_messages_are_updated(): void
  {
    $user         = User::factory()->create();
    $other        = User::factory()->create();
    $conversation = $this->createConversation($user, $other);

    $otherUnread  = $this->createMessage($conversation, $other, 'sent');
    $otherRead    = $this->createMessage($conversation, $other, 'read');
    $ownMessage   = $this->createMessage($conversation, $user, 'sent');

    $this->actingAs($user)->put($this->markAsReadUrl($conversation));

    // Other's unread → now read
    $this->assertDatabaseHas('messages', ['id' => $otherUnread->id, 'status' => 'read']);

    // Other's already-read → unchanged
    $this->assertDatabaseHas('messages', ['id' => $otherRead->id, 'status' => 'read']);

    // Own message → unchanged
    $this->assertDatabaseHas('messages', ['id' => $ownMessage->id, 'status' => 'sent']);
  }

  // -------------------------------------------------------------------------
  // No messages — no error
  // -------------------------------------------------------------------------

  public function test_marking_as_read_on_conversation_with_no_messages_returns_success(): void
  {
    $user         = User::factory()->create();
    $other        = User::factory()->create();
    $conversation = $this->createConversation($user, $other);

    $this->actingAs($user)
      ->put($this->markAsReadUrl($conversation))
      ->assertOk()
    ->assertJson(['success' => true]);
  }

  // -------------------------------------------------------------------------
  // Messages from other conversations are not affected
  // -------------------------------------------------------------------------

  public function test_messages_from_other_conversations_are_not_marked_as_read(): void
  {
    $user         = User::factory()->create();
    $other        = User::factory()->create();
    $third        = User::factory()->create();

    $conversation      = $this->createConversation($user, $other);
    $otherConversation = $this->createConversation($user, $third);

    $unrelatedMessage = $this->createMessage($otherConversation, $third, 'sent');

    $this->actingAs($user)->put($this->markAsReadUrl($conversation));

    // Message in a different conversation should remain untouched
    $this->assertDatabaseHas('messages', [
      'id'     => $unrelatedMessage->id,
      'status' => 'sent',
    ]);
  }

  // -------------------------------------------------------------------------
  // 404 — conversation not found
  // -------------------------------------------------------------------------

  public function test_returns_404_for_a_non_existent_conversation(): void
  {
    $user = User::factory()->create();

    $this->actingAs($user)
      ->put(route('conversations.markAsRead', 99999))
    ->assertNotFound();
  }
}