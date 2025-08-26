<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Report extends Model
{
  protected $fillable = [
    'user_id',
    'type',
    'species',
    'breed',
    'color',
    'sex',
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
    'animal_name',
  ];
  
  public function getImageUrlAttribute()
  {
    if(str_contains($this->image,'reports_images/'))
    {
      return asset('storage/'. $this->image);
    }

    return asset($this->image);
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function getTypeFormattedAttribute(): string
  {
    $type = Str::ucfirst(Str::lower($this->type));
    $species = Str::ucfirst(Str::lower($this->species));
    return "{$type} {$species}";
  }

  public function isLostReport(): bool
  {
    return $this->type === 'lost';
  }

  public function isFoundReport(): bool
  {
    return $this->type === 'found';
  }

  public function foundLastSeenLocation(): string
  {
    if($this->last_seen_location !== null && ($this->found_location === null)) {
      return Str::ucfirst(Str::lower($this->last_seen_location));
    }else if($this->last_seen_location === null && ($this->found_location !== null)) {
      return Str::ucfirst(Str::lower($this->found_location));
    }
     return "";
  }

  public function foundLastSeenDate(): string
  {
    if($this->last_seen_date !== null && ($this->found_date === null)) {
      return \Carbon\Carbon::parse($this->last_seen_date)->format('M d, Y');
    }else if($this->last_seen_date === null && ($this->found_date !== null)) {
      return \Carbon\Carbon::parse($this->found_date)->format('M d, Y');
    }
     return "";
  }

  public function getConditionFormattedAttribute(): string
  {
    if($this->condition === null) {
      return "Unknown";
    }
    return Str::ucfirst(Str::lower($this->condition));
  }

  public function getTemporaryShelterFormattedAttribute(): string
  {
    if($this->temporary_shelter === null) {
      return "Unknown";
    }
    return Str::ucfirst(Str::lower($this->temporary_shelter));
  }

  public function distinctiveFeatures(): string
  {
    if($this->distinctive_features !== null) {
      $text = trim($this->distinctive_features);

      if (!preg_match('/[.!?]$/', $text)) {
        $text .= '.';
      }

      $text = preg_replace('/([.!?])([A-Za-z])/', '$1 $2', $text);

      $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

     $formatted = array_map(function ($sentence) {
        $sentence = Str::lower($sentence);
        return ucfirst(trim($sentence));
      }, $sentences);

      return implode(' ', $formatted);
    }
    return "None";
  }

  public function getSexFormattedAttribute(): string
  {
    return Str::headline($this->sex);
  }

  public function reportedDate(): string
  {
    return \Carbon\Carbon::parse($this->created_at)->format('M d, Y');
  }

  public function getAnimalNameFormattedAttribute(): string
  {
    return Str::headline($this->animal_name);
  }

  public function statusLabel()
  {
    if($this->status === 'active')
    {
      return "Not yet resolved";
    }
    return "Resolved";
  }

  public function isStillActive()
  {
    return $this->status === 'active';
  }
  
  public function getBreedFormattedAttribute(): string
  {
    return Str::ucfirst(Str::lower($this->breed));
  }

  public function getColorFormattedAttribute ()
  {
    return Str::ucfirst(Str::lower($this->color));
  }
  public function getAgeEstimateFormattedAttribute ()
  {
    return Str::ucfirst(Str::lower($this->age_estimate)) .' ' .'old';
  }

  public function getSizeFormattedAttribute ()
  {
    return Str::headline($this->size);
  }

  public function ownerFullName()
  {
    $firstName = Str::ucfirst(Str::lower($this->user->first_name));
    $lastName = Str::ucfirst(Str::lower($this->user->last_name));
    return "{$firstName} {$lastName}";
  }

  public function getContactNumber()
  {
    return $this->user->contact_number;
  }

  public function getEmail()
  {
    return $this->user->email;
  }

  public function ownedByLoggedUser()
  {
    $user = Auth::user();
    if($user?->id === $this->user_id) {
      return true;
    }
    return false;
  }
  public function loggedUserIsAdminOrStaff(){
    $user = Auth::user();

    if($user?->isAdminOrStaff()){
      return true;
    }
  }
}
