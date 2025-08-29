<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\Cast;

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
}
