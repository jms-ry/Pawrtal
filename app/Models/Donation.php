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

  protected $appends = [
    'donation_type_formatted',
    'status_label',
    'amount_formatted',
    'item_description_formatted',
    'item_quantity_formatted',
    'donation_date_formatted'
  ];
  public function user() : BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function getDonationTypeFormattedAttribute()
  {
    return Str::ucfirst($this->donation_type);
  }

  public function getStatusLabelAttribute()
  {
    return Str::headline($this->status);
  }

  public function getAmountFormattedAttribute()
  {
    if($this->amount){
      return '₱ ' + $this->amount;
    }else{
      return 'N/A';
    }
  }

  public function getItemDescriptionFormattedAttribute()
  {
    if($this->item_description){
      return Str::headline($this->item_description);
    }

    return "N/A";
  }

  public function getItemQuantityFormattedAttribute()
  {
    if($this->item_quantity)
    {
      return $this->item_quantity;
    }

    return 'N/A';
  }

  public function getDonationDateFormattedAttribute()
  {
    return \Carbon\Carbon::parse($this->donation_date)->format('M d, Y');
  }
}
