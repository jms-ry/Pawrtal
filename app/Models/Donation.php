<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
class Donation extends Model
{
  use SoftDeletes;
  use HasFactory;
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
    'contact_person',
    'donation_image',
    // New payment-related fields
    'payment_method',
    'payment_intent_id',
    'payment_status',
    'transaction_reference',
    'paid_at',
  ];

  protected $casts = [
    'paid_at' => 'datetime',
  ];

  protected $appends = [
    'donation_type_formatted',
    'status_label',
    'amount_formatted',
    'item_description_formatted',
    'item_quantity_formatted',
    'donation_date_formatted',
    'pick_up_location_formatted',
    'contact_person_formatted',
    'donation_image_url',
    'donor_name_formatted',
    'is_owned_by_logged_user',
    'logged_user_is_admin_or_staff',
    'payment_method_formatted',
    'payment_status_formatted',
  ];

  public function isMonetary()
  {
    return $this->donation_type === 'monetary';
  }

  public function isInKind()
  {
    return $this->donation_type === 'in-kind';
  }

  public function isPaid()
  {
    return $this->payment_status === 'paid';
  }

  public function resolveRouteBinding($value, $field = null)
  {
    return $this->withTrashed()->where($field ?? $this->getRouteKeyName(), $value)->firstOrFail();
  }
  public function getIsOwnedByLoggedUserAttribute()
  {
    $user = Auth::user();

    return $user?->id === $this->user_id ? 'true' : 'false';
  }

  public function getLoggedUserIsAdminOrStaffAttribute()
  {
    $user = Auth::user();

    return $user?->isAdminOrStaff() ? 'true' : 'false';
  }
  public function getDonorNameFormattedAttribute()
  {
    return $this->user ? $this->user->fullName() : 'N/A';
  }
  public function user() : BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function getDonationTypeFormattedAttribute()
  {
    if($this->trashed()){
      return Str::ucfirst($this->donation_type) . ' (Archived)';
    }
    return Str::ucfirst($this->donation_type);
  }

  public function getStatusLabelAttribute()
  {
    return Str::headline($this->status);
  }

  public function getAmountFormattedAttribute()
  {
    if ($this->amount !== null) {
        return '₱ ' . number_format($this->amount, 2);
    }

    return 'N/A';
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

  public function getPickUpLocationFormattedAttribute()
  {
    return Str::headline($this->pick_up_location);
  }

  public function getContactPersonFormattedAttribute()
  {
    return Str::headline($this->contact_person);
  }

  public function getDonationImageUrlAttribute()
  {
    if(str_contains($this->donation_image,'donation_images/'))
    {
      return asset('storage/'. $this->donation_image);
    }

    return asset($this->donation_image);
  }

  public function getPaymentMethodFormattedAttribute()
  {
    if ($this->payment_method) {
      return Str::headline($this->payment_method);
    }

    return 'N/A';
  }

  public function getPaymentStatusFormattedAttribute()
  {
    if ($this->payment_status) {
      return Str::headline($this->payment_status);
    }

    return 'N/A';
  }
}
