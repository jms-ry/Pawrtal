<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
  protected $fillable = [
    'barangay',
    'municipality',
    'province',
    'zip_code',
  ];

  public function users(): HasMany
  {
    return $this->hasMany(User::class, 'address_id');
  }
}
