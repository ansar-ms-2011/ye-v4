<?php

namespace App\Http\Requests\Settings;

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    use ProfileValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [];

        if ($this->user()->isAdmin()) {
            $rules = $this->profileRules($this->user()->id);
        }

        if ($this->user()->isDyeo()) {
            $rules['dyeo_name'] = ['required', 'string', 'max:255'];
            $rules['dyeo_email'] = ['required', 'string', 'email', 'max:255'];
            $rules['district_code'] = ['required', 'numeric'];
            $rules['dyeo_address'] = ['required', 'string', 'max:255'];
            $rules['dyeo_city'] = ['required', 'string', 'max:100'];
            $rules['dyeo_state'] = ['required', 'string', 'max:100'];
            $rules['dyeo_postcode'] = ['required', 'string', 'max:10'];
            $rules['dyeo_country'] = ['required', 'string', 'max:100'];
            $rules['dyeo_mobile'] = ['required', 'string', 'max:15'];

        }

        if ($this->user()->isCyeo()) {
            $rules['cyeo_name'] = ['required', 'string', 'max:255'];
            $rules['cyeo_email'] = ['required', 'string', 'email', 'max:255'];
            $rules['cyeo_address'] = ['required', 'string', 'max:255'];
            $rules['cyeo_city'] = ['required', 'string', 'max:100'];
            $rules['cyeo_state'] = ['required', 'string', 'max:100'];
            $rules['cyeo_postcode'] = ['required', 'string', 'max:100'];
            $rules['cyeo_country'] = ['required', 'string', 'max:10'];
            $rules['cyeo_mobile'] = ['required', 'string', 'max:100'];
            $rules['ribi_club_id'] = ['required', 'string', 'max:15'];
        }

        return $rules;
    }
}
