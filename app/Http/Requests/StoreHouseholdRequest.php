<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHouseholdRequest extends FormRequest
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
      'house_structure' => 'required|string|max:255',
      'household_members' => 'required|integer|min:1',
      'have_children' => 'required|in:true,false',
      'number_of_children' => 'nullable|integer|min:1',
      'has_other_pets' => 'required|in:true,false',
      'current_pets' => 'nullable|string|max:255',
      'number_of_current_pets' => 'nullable|integer|min:1',
    ];
  }
}
