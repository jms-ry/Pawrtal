<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
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
      'user_id' => 'required|exists:users,id',
      'type' => 'required|in:lost,found',
      'species' => 'required|string|max:255',
      'breed' => 'nullable|string|max:255',
      'color' => 'required|string|max:255',
      'sex' => 'required|in:male,female,unknown',
      'age_estimate' => 'nullable|string|max:255',
      'size' => 'required|in:small,medium,large',
      'found_location' => 'nullable|string|max:255',
      'found_date' => 'nullable|date',
      'distinctive_features' => 'nullable|string|max:255',
      'last_seen_location' => 'nullable|string|max:255',
      'last_seen_date' => 'nullable|date',
      'condition' => 'nullable|string|max:255',
      'temporary_shelter' => 'nullable|string|max:255',
      'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
      'status' => 'in:active,resolved',
    ];
  }
}
