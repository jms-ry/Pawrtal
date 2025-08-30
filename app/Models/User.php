<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory, Notifiable;

  /**
    * The attributes that are mass assignable.
    *
    * @var list<string>
  */
  protected $fillable = [
    'first_name',
    'last_name',
    'contact_number',
    'role',
    'email',
    'password',
  ];

  /**
    * The attributes that should be hidden for serialization.
    *
    * @var list<string>
  */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
    * Get the attributes that should be cast.
    *
    * @return array<string, string>
  */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  public function donations() : HasMany
  {
    return $this->hasMany(Donation::class);
  }
  public function adoptionApplications(): HasMany
  {
    return $this->hasMany(AdoptionApplication::class);
  }
  public function canAdopt(){
    if($this->isNonAdminOrStaff()){
      if($this->address && $this->household){
        return true;
      }else{
        return false;
      }
    }else{
      return false;
    }
  }
  public function household(): HasOne
  {
    return $this->hasOne(Household::class);
  }
  public function address(): HasOne
  {
    return $this->hasOne(Address::class);
  }
  public function reports(): HasMany
  {
    return $this->hasMany(Report::class,'user_id');
  }

  public function isAdminOrStaff(): bool
  {
    return $this->role === 'admin' || $this->role === 'staff';
  }

  public function fullName(): string
  {
    return $this->first_name . ' ' . $this->last_name;
  }

  public function getRole()
  {
    return Str::headline($this->role);
  }

  public function isNonAdminOrStaff(): bool
  {
    return $this->role !== 'admin' && $this->role !== 'staff';
  }
}
