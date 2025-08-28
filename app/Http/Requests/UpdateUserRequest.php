<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
      'first_name' => 'nullable|string|max:255',
      'last_name' => 'nullable|string|max:255',
      'contact_number' => 'nullable|string|max:20',
      'role' => 'nullable|in:admin,staff,user',
      'email' => 'nullable|email|max:255|unique:users,email,' . $this->user->id,
      'address_id' => 'nullable|exists:addresses,id',
      'household_id' => 'nullable|exists:households,id',
      'password' => 'nullable|string|min:8|confirmed',
    ];
  }
}
