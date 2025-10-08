<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
class MessageController extends Controller
{
  public function index(Conversation $conversation)
  {

  }

  public function store(Request $request, Conversation $conversation)
  {

  }

  public function markAsRead(Message $message)
  {

  }

  public function markAllAsRead(Conversation $conversation)
  {
    
  }
}
