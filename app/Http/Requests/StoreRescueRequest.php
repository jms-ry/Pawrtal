<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRescueRequest extends FormRequest
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
      'name' => 'required|string|max:255',
      'species' => 'required|string|max:255',
      'breed' => 'nullable|string|max:255',
      'description' => 'required|string|max:5000',
      'sex' => 'required|in:male,female',
      'age' => 'nullable|string|max:255',
      'size' => 'nullable|in:small,medium,large',
      'color' => 'nullable|string|max:255',
      'distinctive_features' => 'nullable|string|max:255',
      'health_status' => 'required|in:healthy,sick,injured',
      'vaccination_status' => 'required|in:vaccinated,not_vaccinated,partially_vaccinated',
      'spayed_neutered' => 'required|in:true,false',
      'adoption_status' => 'required|in:available,adopted,unavailable',
      'profile_image' => 'required|image|mimes:jpeg,png,jpg,|max:2048',
      'images' => 'nullable|array|max:5',
      'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',

    ];
  }
}
