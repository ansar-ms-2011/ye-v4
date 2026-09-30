<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CyeoStoreFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole('admin', 'dyeo');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cyeo_name' => ['required', 'string', 'max:255'],
            'cyeo_email' => ['required', 'string', 'email', 'max:255', 'unique:ribi_cyeo'],
            'ribi_club_id' => ['required', 'integer', 'exists:ribi_clubs,id'],
        ];
    }
}
