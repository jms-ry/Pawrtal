<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Donation extends Model
{
  public const CREATED_AT = 'donation_date';
  public const UPDATED_AT = 'updated_at';
  protected $fillable = [
    'user_id',
    'donation_type',
    'amount',
    'item_description',
    'item_quantity',
    'status',
    'donation_date',
    'pick_up_location',
    'contact_person'
  ];

  public function user() : BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function getDonationTypeFormatted()
  {
    return Str::ucfirst($this->donation_type);
  }
}
