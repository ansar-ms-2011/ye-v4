<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DyeoStoreFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dyeo_name' => ['required', 'string', 'max:255'],
            'dyeo_email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('ribi_dyeo', 'dyeo_email')->ignore($this->route('dyeo')),
            ],
            'district_code' => ['required', 'integer'],
        ];
    }
}
