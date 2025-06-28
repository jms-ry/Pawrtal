<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
  protected $fillable = [
    'user_id',
    'type',
    'species',
    'breed',
    'color',
    'age_estimate',
    'size',
    'found_location',
    'found_date',
    'distinctive_features',
    'last_seen_location',
    'last_seen_date',
    'condition',
    'temporary_shelter',
    'image',
    'status',
  ];
  
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }
}
