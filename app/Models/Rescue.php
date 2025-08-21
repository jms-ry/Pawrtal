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

  public function getProfileImageUrlAttribute()
  {
    if(str_contains($this->profile_image,'profile_images/'))
    {
      return asset('storage/'. $this->profile_image);
    }

    return asset($this->profile_image);
  }
  
  public function getImagesUrlAttribute()
  {
    if (is_array($this->images) && count($this->images) > 0) {
      return array_map(function ($image) {
        if(str_contains($image,'/gallery_images'))
        {
          return asset('storage/'. $image);
        }
        return asset($image);
      }, $this->images);
    }
    return [];
  }
  
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

  public function isAdopted ()
  {
    return $this->adoption_status === 'adopted';
  }

  public function isAvailable ()
  {
    return $this->adoption_status === 'available';
  }

  public function isUnavailable ()
  {
    return $this->adoption_status === 'unavailable';
  }

  public function tagLabel()
  {
    $age = $this->age_formatted;
    $sex = $this->sex_formatted;

    return "$sex, $age";
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
}
