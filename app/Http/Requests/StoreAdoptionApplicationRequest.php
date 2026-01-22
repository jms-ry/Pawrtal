<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreAdoptionApplicationRequest extends FormRequest
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
      'rescue_id' => [
        'required',
        Rule::exists('rescues', 'id')->where(fn ($query) =>
          $query->where('adoption_status', 'available')
        ),
      ],
      'status' => 'in:pending,approved,rejected,under_review,cancelled',
      'reason_for_adoption' => 'required|string|max:5000',
      'preferred_inspection_start_date' => 'required|date|after_or_equal:today',
      'preferred_inspection_end_date' => [
        'required',
        'date',
        'after_or_equal:today',
        'after_or_equal:preferred_inspection_start_date'
      ],
      'valid_id' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
      'supporting_documents' =>'required|array|',
      'supporting_documents.*'=> 'file|mimes:jpg,jpeg,png,pdf,doc|max:5120',
      'reviewed_by' => 'nullable|string|max:255',
      'review_date' =>'nullable|date',
      'review_notes' => 'nullable|string|max:5000'
    ];
  }
}
