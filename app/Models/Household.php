<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Household extends Model
{
  protected $fillable = [
    'house_structure',
    'household_members',
    'have_children',
    'number_of_children',
    'has_other_pets',
    'current_pets',
    'number_of_current_pets',
    'user_id'
  ];

  public function user(): BelongsTo 
  {
    return $this->belongsTo(User::class);
  }
}
