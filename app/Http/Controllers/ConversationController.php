<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ConversationController extends Controller
{
  public function index()
  {
    $user = Auth::user();
    $previousUrl = url()->previous();

    // Retrieve all conversations with messages and participants
    $conversations = Conversation::where(function ($query) use ($user) {
      $query->where('participant1_id', $user->id)
        ->orWhere('participant2_id', $user->id);
        })
        ->with([
          'messages' => function ($query) {
            $query->orderBy('created_at', 'asc');
          },
          'participant1:id,first_name,last_name,email,role',
          'participant2:id,first_name,last_name,email,role'
        ])
        ->orderBy('last_message_at', 'desc')
      ->get()
    ->map(function ($conversation) use ($user) {
      // Add unread count for the current user
      $conversation->unread = $conversation->messages()
        ->where('sender_id', '!=', $user->id)
        ->where('status', '!=', 'read')
        ->count();
      return $conversation;
    });

    // Get user IDs that already have conversations
    $conversationUserIds = $conversations->flatMap(function ($conversation) use ($user) {
      return [
        $conversation->participant1_id === $user->id 
        ? $conversation->participant2_id 
        : $conversation->participant1_id
      ];
    })->unique()->toArray();

    // Get users without conversations
    $users = User::where('id', '!=', $user->id)
      ->whereNotIn('id', $conversationUserIds)
      ->select('id', 'first_name', 'last_name', 'email', 'role')
    ->get();

    return Inertia::render('Conversation/Index', [
      'previousUrl'   => $previousUrl,
      'users'         => $users,
      'conversations' => $conversations,
      'user'          => $user,
    ]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'participant_id' => 'required|exists:users,id|different:' . Auth::id(),
    ]);

    $user = Auth::user();
    $participantId = $request->participant_id;

    // Check if conversation already exists
    $existingConversation = Conversation::where(function ($query) use ($user, $participantId) {
      $query->where(function ($q) use ($user, $participantId) {
        $q->where('participant1_id', $user->id)
          ->where('participant2_id', $participantId);
        })->orWhere(function ($q) use ($user, $participantId) {
          $q->where('participant1_id', $participantId)
        ->where('participant2_id', $user->id);
      });
    })->first();

    if ($existingConversation) {
      return redirect()->route('conversations.index');
    }

    // Create new conversation
    $conversation = Conversation::findOrCreate($user->id, $participantId);

    return redirect()->route('conversations.index')->with('success', 'Conversation started successfully');
  }

  // public function show(Conversation $conversation)
  // {
  //   $user = Auth::user();

  //   // Ensure user is part of the conversation
  //   if ($conversation->participant1_id !== $user->id && $conversation->participant2_id !== $user->id) {
  //     abort(403, 'Unauthorized access to this conversation.');
  //   }

  //   // Mark messages as read
  //   $conversation->messages()
  //     ->where('sender_id', '!=', $user->id)
  //     ->where('status', '!=', 'read')
  //   ->update(['status' => 'read']);

  //   $conversation->load([
  //     'messages' => function ($query) {
  //       $query->orderBy('created_at', 'asc');
  //     },
  //     'participant1:id,first_name,last_name,email,role',
  //     'participant2:id,first_name,last_name,email,role'
  //   ]);

  //   return Inertia::render('Conversation/Show', [
  //     'conversation' => $conversation,
  //     'user' => $user,
  //   ]);
  // }

  // public function markAsRead(Conversation $conversation)
  // {
  //   $user = Auth::user();

  //   // Ensure user is part of the conversation
  //   if ($conversation->participant1_id !== $user->id && $conversation->participant2_id !== $user->id) {
  //     abort(403);
  //   }

  //   // Mark all messages from other participant as read
  //   $conversation->messages()
  //     ->where('sender_id', '!=', $user->id)
  //     ->where('status', '!=', 'read')
  //   ->update(['status' => 'read']);

  //   return response()->json(['success' => true]);
  // }
}