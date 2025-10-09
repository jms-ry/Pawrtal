<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
class ConversationController extends Controller
{
  public function index()
  {
    $user = Auth::user();
    $previousUrl = url()->previous();

    // Retrieve all conversations with their participants
    $conversations = $user->conversations()
      ->with(['messages', 'participant1', 'participant2'])
    ->get();

    // Collect all user IDs that the logged-in user has conversations with
    $conversationUserIds = $conversations->flatMap(function ($conversation) use ($user) {
      return [$conversation->participant1_id, $conversation->participant2_id];
    })->unique();

    // Exclude the logged-in user and those already in a conversation
    $users = User::whereKeyNot($user->id)
      ->whereNotIn('id', $conversationUserIds)
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
    
  }
  public function show(Conversation $conversation)
  {
    
  }
}
