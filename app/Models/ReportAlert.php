<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportAlert extends Model
{
  public $timestamps = false;
    
  protected $fillable = [
    'user_id',
    'report_id',
    'alerted_at',
  ];

  protected $casts = [
    'alerted_at' => 'datetime',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function report(): BelongsTo
  {
    return $this->belongsTo(Report::class);
  }
}