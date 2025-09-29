<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
      'rescue_id' => 'required|exists:rescues,id',
      'status' => 'required|in:pending,approved,rejected,under_review,cancelled',
      'reason_for_adoption' => 'required|string|max:5000',
      'preferred_inspection_start_date' => 'required|date',
      'preferred_inspection_end_date' => 'required|date',
      'valid_id' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
      'supporting_documents' =>'required|array|',
      'supporting_documents.*'=> 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
      'reviewed_by' => 'nullable|string|max:255',
      'reviewed_date' =>'nullable|date',
      'review_notes' => 'nullable|string|max:5000'
    ];
  }
}
