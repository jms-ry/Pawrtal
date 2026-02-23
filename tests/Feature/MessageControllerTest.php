<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
  use RefreshDatabase;

  // -------------------------------------------------------------------------
  // Helpers
  // -------------------------------------------------------------------------

  private function createConversation(?User $participant1 = null, ?User $participant2 = null): array
  {
    $participant1 ??= User::factory()->create();
    $participant2 ??= User::factory()->create();

    $conversation = Conversation::factory()->create([
      'participant1_id' => $participant1->id,
      'participant2_id' => $participant2->id,
    ]);

    return [$conversation, $participant1, $participant2];
  }

  private function storeUrl(Conversation $conversation): string
  {
    return route('messages.store', $conversation);
  }

  // -------------------------------------------------------------------------
  // Authentication
  // -------------------------------------------------------------------------

  public function test_guest_cannot_send_a_message(): void
  {
    [$conversation] = $this->createConversation();

    $this->post($this->storeUrl($conversation), ['content' => 'Hello!'])->assertRedirect(route('login'));
  }

  // -------------------------------------------------------------------------
  // Authorization
  // -------------------------------------------------------------------------

  public function test_participant1_can_send_a_message(): void
  {
    [$conversation, $participant1] = $this->createConversation();

    $this->actingAs($participant1)->post($this->storeUrl($conversation), ['content' => 'Hello from participant 1!'])->assertRedirect(route('conversations.index'));
  }

  public function test_participant2_can_send_a_message(): void
  {
    [$conversation, , $participant2] = $this->createConversation();

    $this->actingAs($participant2)
      ->post($this->storeUrl($conversation), ['content' => 'Hello from participant 2!'])
    ->assertRedirect(route('conversations.index'));
  }

  public function test_non_participant_is_forbidden_from_sending_a_message(): void
  {
    [$conversation] = $this->createConversation();
    $outsider = User::factory()->create();

    $this->actingAs($outsider)
      ->post($this->storeUrl($conversation), ['content' => 'I should not be here.'])
    ->assertForbidden();
  }

  // -------------------------------------------------------------------------
  // Validation — content field
  // -------------------------------------------------------------------------

  public function test_content_is_required(): void
  {
    [$conversation, $participant1] = $this->createConversation();

    $this->actingAs($participant1)
      ->post($this->storeUrl($conversation), ['content' => ''])
    ->assertSessionHasErrors('content');
  }

  public function test_content_must_be_a_string(): void
  {
    [$conversation, $participant1] = $this->createConversation();

    $this->actingAs($participant1)
      ->post($this->storeUrl($conversation), ['content' => ['array', 'value']])
    ->assertSessionHasErrors('content');
  }

  public function test_content_cannot_exceed_2000_characters(): void
  {
    [$conversation, $participant1] = $this->createConversation();

    $this->actingAs($participant1)
      ->post($this->storeUrl($conversation), ['content' => str_repeat('a', 2001)])
    ->assertSessionHasErrors('content');
  }

  public function test_content_at_max_length_is_accepted(): void
  {
    [$conversation, $participant1] = $this->createConversation();

    $this->actingAs($participant1)
      ->post($this->storeUrl($conversation), ['content' => str_repeat('a', 2000)])
    ->assertRedirect(route('conversations.index'));
  }

  // -------------------------------------------------------------------------
  // Message persistence
  // -------------------------------------------------------------------------

  public function test_sending_a_message_persists_it_to_the_database(): void
  {
    [$conversation, $participant1] = $this->createConversation();

    $this->actingAs($participant1)->post($this->storeUrl($conversation), ['content' => 'Persisted message']);

    $this->assertDatabaseHas('messages', [
      'conversation_id' => $conversation->id,
      'sender_id'       => $participant1->id,
      'content'         => 'Persisted message',
      'status'          => 'sent',
    ]);
  }

  public function test_message_status_defaults_to_sent(): void
  {
    [$conversation, $participant1] = $this->createConversation();

    $this->actingAs($participant1)->post($this->storeUrl($conversation), ['content' => 'Status check']);

    $message = Message::where('sender_id', $participant1->id)->first();

    $this->assertSame('sent', $message->status);
  }

  // -------------------------------------------------------------------------
  // Conversation last_message_at update
  // -------------------------------------------------------------------------

  public function test_sending_a_message_updates_conversations_last_message_at(): void
  {
    [$conversation, $participant1] = $this->createConversation();

    $this->actingAs($participant1)->post($this->storeUrl($conversation), ['content' => 'Timestamp test']);

    $message = Message::where('sender_id', $participant1->id)->first();

    $this->assertDatabaseHas('conversations', [
      'id'              => $conversation->id,
      'last_message_at' => $message->created_at,
    ]);
  }

  // -------------------------------------------------------------------------
  // Database transaction integrity
  // -------------------------------------------------------------------------

  public function test_message_and_conversation_are_saved_atomically(): void
  {
    [$conversation, $participant1] = $this->createConversation();

    $this->actingAs($participant1)->post($this->storeUrl($conversation), ['content' => 'Atomic write']);

    $this->assertDatabaseCount('messages', 1);
    $this->assertNotNull($conversation->fresh()->last_message_at);
  }

    // -------------------------------------------------------------------------
    // Redirect behaviour
    // -------------------------------------------------------------------------

  public function test_user_is_redirected_to_conversations_index_after_sending(): void
  {
    [$conversation, $participant1] = $this->createConversation();

    $this->actingAs($participant1)
      ->post($this->storeUrl($conversation), ['content' => 'Redirect check'])
    ->assertRedirect(route('conversations.index'));
  }

  // -------------------------------------------------------------------------
  // 404 — conversation not found
  // -------------------------------------------------------------------------

  public function test_returns_404_for_a_non_existent_conversation(): void
  {
    $user = User::factory()->create();

    $this->actingAs($user)
      ->post(route('messages.store', 99999), ['content' => 'Ghost conversation'])
    ->assertNotFound();
  }
}