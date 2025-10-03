<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInspectionScheduleRequest extends FormRequest
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
      'application_id' => 'nullable', 'exists:adoption_applications,id',
      'inspector_id' => 'nullable', 'exists:users,id',
      'inspection_location' => 'nullable','string','max:255',
      'inspection_date' => 'nullable','date',
      'status' => 'nullable|in:upcoming,now,done,cancelled,missed',
    ];
  }
}
