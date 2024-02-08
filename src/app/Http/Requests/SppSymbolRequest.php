<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SppSymbolRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'required' => 'Pole :attribute je povinné.',
            'string' => 'Pole :attribute musí byť reťazec.',
            'max' => 'Pole :attribute môže mať maximálne :max znakov.',
            'unique' => 'Pole :attribute už existuje.',
            'exists' => 'Vybrané pole :attribute neexistuje.',
        ];
    }
    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'fund' => 'fond',
            'spp_symbol' => 'symbol ŠPP',
            'functional_region' => 'funkčná oblasť',
            'account' => 'účet',
            'financial_centre' => 'finančné centrum',
            'grantee' => 'príjemca',
            'spp' => 'ŠPP prvok',
        ];
    }
}
