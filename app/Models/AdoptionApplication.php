<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpParser\Node\Expr\Cast;
use Illuminate\Support\Facades\Auth;
use Str;

class AdoptionApplication extends Model
{
  use SoftDeletes;
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
    'applicant_full_name',
    'archived',
    'applicant_full_address',
    'applicant_house_structure',
    'applicant_household_members',
    'applicant_number_of_children',
    'applicant_number_of_current_pets',
    'applicant_current_pets',
    'logged_user_is_admin_or_staff',
    'inspection_location',
    'inspector_name',
    'inspection_date',
    'supporting_documents_url',
    'review_notes_formatted',
    'reviewed_date_formatted',
    'reviewed_by_formatted'
  ];
  protected $casts = [
    'supporting_documents' => 'array'
  ];
  
  public function getReviewNotesFormattedAttribute()
  {
    return $this->review_notes;
  }

  public function getReviewedDateFormattedAttribute()
  {
    return \Carbon\Carbon::parse($this->reviewed_date)->format('M d, Y');
  }

  public function getReviewedByFormattedAttribute()
  {
    return Str::headline($this->reviewed_by);
  }
  public function inspectionSchedule()
  {
    return $this->hasOne(InspectionSchedule::class,'application_id');
  }

  public function getInspectionLocationAttribute()
  {
    return $this->inspectionSchedule?->inspectionLocation();
  }

  public function getInspectorNameAttribute()
  {
    return $this->inspectionSchedule?->inspectorName();
  }

  public function getInspectionDateAttribute()
  {
    return $this->inspectionSchedule?->inspectionDate();
  }
  public function getLoggedUserIsAdminOrStaffAttribute()
  {
    $user = Auth::user();

    return $user?->isAdminOrStaff() ? 'true' : 'false';
  }
  public function getApplicantFullAddressAttribute()
  {
    return $this->user?->fullAddress();
  }

  public function getApplicantHouseStructureAttribute()
  {
    return $this->user?->household?->houseStructure();
  }

  public function getApplicantHouseholdMembersAttribute()
  {
    return $this->user?->household?->householdMembers();
  }

  public function getApplicantNumberOfChildrenAttribute()
  {
    return $this->user?->household?->numberOfChildren();
  }

  public function getApplicantNumberOfCurrentPetsAttribute()
  {
    return $this->user?->household?->numberOfCurrentPets();
  }

  public function getApplicantCurrentPetsAttribute()
  {
    return $this->user?->household?->currentPets();
  }
  public function getArchivedAttribute()
  {
    return $this->trashed() ? 'Yes' : 'No';
  }

  public function resolveRouteBinding($value, $field = null)
  {
    return $this->withTrashed()->where($field ?? $this->getRouteKeyName(), $value)->firstOrFail();
  }
  public function user()
  {
    return $this->belongsTo(User::class);
  }
  public function rescue()
  {
    return $this->belongsTo(Rescue::class)->withTrashed();
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
    return Str::of($this->status)->replace('_',' ')->title();
  }

  public function getApplicationDateFormattedAttribute()
  {
    return \Carbon\Carbon::parse($this->application_date)->format('M d, Y');
  }

  public function getRescueNameFormattedAttribute()
  {
    if($this->rescue && $this->rescue->trashed()){
      return Str::headline($this->rescue->name) . ' (Archived)';
    }
    return $this->rescue ? Str::headline($this->rescue->name) : '(Rescue Profile Permanently Deleted)';
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

  public function getSupportingDocumentsUrlAttribute()
  {
      
    if (empty($this->supporting_documents) || !is_array($this->supporting_documents)) {
      return [];
    }

    return array_map(function ($document) {
      if (str_contains($document, 'supporting_documents/')) {
        return asset('storage/' . $document);
      }
      return asset($document);
    }, $this->supporting_documents);
  }
}
