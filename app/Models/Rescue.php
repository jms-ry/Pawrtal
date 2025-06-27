<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Str;

class Rescue extends Model
{
  protected $fillable = [
    'name',
    'species',
    'breed',
    'description',
    'sex',
    'age',
    'size',
    'color',
    'distinctive_features',
    'health_status',
    'vaccination_status',
    'spayed_neutered',
    'adoption_status',
    'profile_image',
    'images',
  ];

  protected $casts = [
    'images' => 'array',
  ];
  
  public function getSexFormattedAttribute ()
  {
    return Str::headline($this->sex);
  }

  public function getAgeFormattedAttribute ()
  {
    return Str::ucfirst(Str::lower($this->age)) .' ' .'old';
  }

  public function getColorFormattedAttribute ()
  {
    return Str::ucfirst(Str::lower($this->color));
  }

  public function getDescriptionFormattedAttribute ()
  {
    $text = trim($this->description);

    
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

  public function getSizeFormattedAttribute ()
  {
    return Str::headline($this->size);
  }

  public function getVaccinationStatusFormattedAttribute ()
  {
    return Str::headline($this->vaccination_status);
  }

  public function getSpayedNeuteredFormattedAttribute ()
  {
    return $this->spayed_neutered ? 'Yes' : 'No';
  }
}
