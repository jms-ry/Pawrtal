<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
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
      'user_id' => 'required|exists:users,id',
      'donation_type' => 'required|in:monetary,in-kind',
      'amount' => 'nullable|numeric|min:0',
      'item_description' => 'nullable|string|max:5000',
      'item_quantity' => 'nullable|integer|min:1',
      'status'=> 'required|in:pending,approved,picked_up,rejected',
      'pick_up_location' => 'nullable|string|max:5000',
      'contact_person' => 'nullable|string|max:255' 
    ];
  }
}
