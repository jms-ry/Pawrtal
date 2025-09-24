<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\Cast;
use Str;

class AdoptionApplication extends Model
{
  public const CREATED_AT = 'application_date';
  public const UPDATED_AT = 'updated_at';
  protected $fillable = [
    'user_id',
    'rescue_id',
    'application_date',
    'status',
    'reason_for_adoption',
    'preferred_inspection_start_date',
    'preferred_inspection_end_date',
    'valid_id',
    'supporting_documents',
    'reviewed_by',
    'review_date',
    'review_notes',
  ];
  protected $appends = [
    'status_label',
    'application_date_formatted',
    'rescue_name_formatted',
    'inspection_start_date_formatted',
    'inspection_end_date_formatted',
    'reason_for_adoption_formatted',
    'valid_id_url',
    'applicant_full_name'
  ];
  protected $casts = [
    'supporting_documents' => 'array'
  ];
  public function user()
  {
    return $this->belongsTo(User::class);
  }
  public function rescue()
  {
    return $this->belongsTo(Rescue::class);
  }

  public function getApplicantFullNameAttribute()
  {
    if($this->user){
      return $this->user->fullName();
    }
    return "Deleted User";
  }
  public function getStatusLabelAttribute()
  {
    return Str::of($this->status)->replace('_',' ')->ucfirst();
  }

  public function getApplicationDateFormattedAttribute()
  {
    return \Carbon\Carbon::parse($this->application_date)->format('M d, Y');
  }

  public function getRescueNameFormattedAttribute()
  {
    if($this->rescue){
      return Str::headline($this->rescue->name);
    }
    return "Deleted Rescue";
  }

  public function getInspectionStartDateFormattedAttribute()
  {
    return \Carbon\Carbon::parse($this->preferred_inspection_start_date)->format('M d, Y');
  }

  public function getInspectionEndDateFormattedAttribute()
  {
    return \Carbon\Carbon::parse($this->preferred_inspection_end_date)->format('M d, Y');
  }

  public function getReasonForAdoptionFormattedAttribute()
  {
    return Str::of($this->reason_for_adoption)->replace('_',' ')->ucfirst();
  }

  public function getValidIdUrlAttribute()
  {
    if(str_contains($this->valid_id,'valid_ids/'))
    {
      return asset('storage/'. $this->valid_id);
    }

    return asset($this->valid_id);
  }
}
