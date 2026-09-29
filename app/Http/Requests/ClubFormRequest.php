<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ClubFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin', 'dyeo']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'club_name' => 'required|string|max:255',
            'club_president' => 'nullable|string|max:255',
            'club_president_email' => 'nullable|string|email|max:255',
            'club_president_mobile' => 'nullable|string|max:255',
            'club_president_sig' => 'nullable|string|max:255',
            'club_other_name' => 'nullable|string|max:255',
            'club_other_sig' => 'nullable|string|max:255',
            'district_id' => 'required|exists:districts,id',
        ];
    }
}
