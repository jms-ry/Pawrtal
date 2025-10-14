<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
  use HasFactory;
  protected $fillable = [
    'barangay',
    'municipality',
    'province',
    'zip_code',
    'user_id',
  ];

  public function user() : BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function fullAddress() : string
  {
    return "{$this->barangay}, {$this->municipality}, {$this->province}, {$this->zip_code}";
  }
}
