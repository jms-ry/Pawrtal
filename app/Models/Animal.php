<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Animal extends Model
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
}
