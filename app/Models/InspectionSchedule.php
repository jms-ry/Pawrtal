<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class InspectionSchedule extends Model
{
  protected $fillable = [
    'application_id',
    'inspector_id',
    'inspection_location',
    'inspection_date',
    'status'
  ];

  public function adoptionApplication()
  {
    return $this->belongsTo(AdoptionApplication::class,'application_id');
  }

  public function user()
  {
    return $this->belongsTo(User::class,'inspector_id');
  }

  public function inspectionLocation()
  {
    return Str::headline($this->inspection_location);
  }

  public function inspectorName()
  {
    if($this->inspector_id){
      return $this->user?->fullName();
    }
  }

  public function inspectionDate(){
    return \Carbon\Carbon::parse($this->inspection_date)->format('M d, Y');
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      if (empty($model->status)) {
        $today = now()->toDateString();
        if ($model->inspection_date == $today) {
          $model->status = 'now';
        } else {
          $model->status = 'upcoming';
        }
      }
    });
  }

  public function inspectionStatus()
  {
    return $this->getStatusAttribute($this->status);
  }

  public function getStatusAttribute($value)
  {
    if (in_array($value, ['done', 'cancelled'])) {
      return $value;
    }
    
    $today = now()->toDateString();
    
    if ($this->inspection_date == $today) {
      return 'now';
    }else if($this->inspection_date < $today){
      return 'missed';
    }
    
    return 'upcoming';
  }
}
