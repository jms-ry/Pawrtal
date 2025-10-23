<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportRequest extends FormRequest
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
      'animal_name' => 'nullable|string|max:255',
      'species' => 'nullable|string|max:255',
      'breed' => 'nullable|string|max:255',
      'color' => 'nullable|string|max:255',
      'sex' => 'nullable|in:male,female,unknown',
      'age_estimate' => 'nullable|string|max:255',
      'size' => 'nullable|in:small,medium,large',
      'found_location' => 'nullable|string|max:255',
      'found_date' => 'nullable|date|before_or_equal:today',
      'distinctive_features' => 'nullable|string|max:255',
      'last_seen_location' => 'nullable|string|max:255',
      'last_seen_date' => 'nullable|date|before_or_equal:today',
      'condition' => 'nullable|string|max:255',
      'temporary_shelter' => 'nullable|string|max:255',
      'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
      'status' => 'in:active,resolved',
    ];
  }
}
