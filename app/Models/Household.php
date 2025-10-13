<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Household extends Model
{
  use HasFactory;
  protected $fillable = [
    'house_structure',
    'household_members',
    'have_children',
    'number_of_children',
    'has_other_pets',
    'current_pets',
    'number_of_current_pets',
    'user_id'
  ];

  public function user(): BelongsTo 
  {
    return $this->belongsTo(User::class);
  }

  public function houseStructure()
  {
    return Str::ucfirst($this->house_structure);
  }

  public function householdMembers()
  {
    return $this->household_members;
  }

  public function numberOfChildren()
  {
    if($this->number_of_children == 1) {
      return 'There is '. $this->number_of_children . ' child in this household.';
    } else if($this->number_of_children > 1) {
      return 'There are '. $this->number_of_children . ' children in this household.';
    } else {
      return 'N/A';
    }
  }

  public function numberOfCurrentPets()
  {
    if($this->number_of_current_pets == 1) {
      return 'There is '. $this->number_of_current_pets . ' pet in this household.';
    } else if($this->number_of_current_pets > 1) {
      return 'There are '. $this->number_of_current_pets . ' pets in this household.';
    } else {
      return 'N/A';
    }
  }

  public function currentPets()
  {
    if($this->current_pets){
      return $this->current_pets;
    }

    return "N/A";
  }
}
