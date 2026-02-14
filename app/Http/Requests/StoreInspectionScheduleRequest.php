<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\AdoptionApplication;
use Carbon\Carbon;
use App\Models\User;
class StoreInspectionScheduleRequest extends FormRequest
{
  /**
    * Determine if the user is authorized to make this request.
  */
  public function authorize(): bool
  {
    return true;
  }

  /**
    * Get the validation rules that apply to the request.
    *
    * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
  */
  public function rules(): array
  {
    return [
      'application_id' => [
        'required',
        'exists:adoption_applications,id',
        function ($attribute, $value, $fail) {
          $application = AdoptionApplication::with('inspectionSchedule')->find($value);
          if (!$application) {
            return;
          }
          
          if ($application->status !== 'pending') {
            $fail('The selected application must be a pending application.');
          }

          if ($application->inspectionSchedule) {
            $fail('This application already has an inspection schedule.');
          }
        },
      ],
      'inspector_id' => [
        'required',
        'exists:users,id',
        function ($attribute, $value, $fail) {
          $user = User::find($value);
          if (!$user || !in_array($user->role, ['admin', 'staff'])) {
            $fail('The selected inspector must be an admin or staff user.');
          }
        },
      ],

      'inspection_location' => 'required|string|max:255',
      'inspection_date' => [
        'required',
        'date',
        function ($attribute, $value, $fail) {

          if (!strtotime($value)) {
            return; // Let the 'date' rule handle it
          }
          $application = AdoptionApplication::find($this->application_id);
                
          if (!$application) {
            $fail('The selected application is invalid.');
              return;
            }
                
            $inspectionDate = Carbon::parse($value);
            $startDate = Carbon::parse($application->preferred_inspection_start_date);
            $endDate = Carbon::parse($application->preferred_inspection_end_date);
                
            if (!$inspectionDate->between($startDate, $endDate)) {
              $fail("The inspection date must be between {$startDate->format('Y-m-d')} and {$endDate->format('Y-m-d')}.");
            }
          },
        ],
      
    ];
  }
}
