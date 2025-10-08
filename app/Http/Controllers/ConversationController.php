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
    $users = User::whereKeyNot($user->id)->get();
    $conversations = $user->conversations()->with(['messages', 'participant1', 'participant2'])->get();

    return Inertia::render('Conversation/Index',[
      'previousUrl' => $previousUrl,
      'users' => $users,
      'conversations' => $conversations,
      'user' => $user
    ]);
  }

  public function store(Request $request)
  {
    
  }
  public function show(Conversation $conversation)
  {
    
  }
}
