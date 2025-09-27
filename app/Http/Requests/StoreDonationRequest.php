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

      // multiple items
      'item_description'   => 'required_if:donation_type,in-kind|array',
      'item_description.*' => 'string|max:5000',

      'item_quantity'   => 'required_if:donation_type,in-kind|array',
      'item_quantity.*' => 'integer|min:1',

      'donation_image'   => 'required_if:donation_type,in-kind|array',
      'donation_image.*' => 'image|mimes:jpg,jpeg,png,gif|max:2048',

      'pick_up_location'   => 'nullable|array',
      'pick_up_location.*' => 'nullable|string|max:5000',

      'contact_person'   => 'nullable|array',
      'contact_person.*' => 'nullable|string|max:255',

      'status' => 'required|in:pending,accepted,rejected,cancelled',
    ];
  }

}
