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
    'rescue_name_formatted'
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
    return Str::headline($this->rescue->name);
  }
}
