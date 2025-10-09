<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
  protected $fillable = [
    'participant1_id', 'participant2_id', 'last_message_at',
  ];

  protected $casts = [
    'last_message_at' => 'datetime',
  ];
  public function messages()
  {
    return $this->hasMany(Message::class);
  }

  public function participant1()
  {
    return $this->belongsTo(User::class, 'participant1_id');
  }

  public function participant2()
  {
    return $this->belongsTo(User::class, 'participant2_id');
  }

  public function otherParticipant(int $currentUserId)
  {
    return $this->participant1_id === $currentUserId ? $this->participant2 : $this->participant1;
  }

  public static function findOrCreate($userId1, $userId2)
  {
    $participant1 = min($userId1, $userId2);
    $participant2 = max($userId1, $userId2);
    
    return self::firstOrCreate([
      'participant1_id' => $participant1,
      'participant2_id' => $participant2,
    ]);
  }
}

