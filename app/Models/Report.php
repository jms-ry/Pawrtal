<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Str;

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

  public function getTypeFormattedAttribute(): string
  {
    $species = Str::ucfirst(Str::lower($this->species));
    return "{$this->type} {$species}";
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
    return Str::ucfirst(Str::lower($this->condition));
  }

  public function getTemporaryShelterFormattedAttribute(): string
  {
    return Str::ucfirst(Str::lower($this->temporary_shelter));
  }

  public function distinctiveFeatures(): string
  {
    if($this->distinctive_features !== null) {
      return Str::ucfirst(Str::lower($this->distinctive_features));
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
}
