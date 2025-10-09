<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MessageController extends Controller
{
  public function store(Request $request, Conversation $conversation)
  {
    $request->validate([
      'content' => 'required|string|max:2000',
    ]);

    $user = Auth::user();

        
    if ($conversation->participant1_id !== $user->id && $conversation->participant2_id !== $user->id) {
      abort(403);
    }

    DB::transaction(function () use ($conversation, $user, $request, &$message) {
      $message = Message::create([
        'conversation_id' => $conversation->id,
        'sender_id'       => $user->id,
        'content'         => $request->content,
        'status'          => 'sent', 
      ]);
            
       $conversation->last_message_at = $message->created_at;
       $conversation->save();
    });
        
    return redirect()->route('conversations.index');
  }
}
