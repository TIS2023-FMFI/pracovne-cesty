<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class SppSymbolRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        Log::info('SppSymbolRequest: authorize method called');
        return true;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        Log::info('SppSymbolRequest: messages method called');
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
        Log::info('SppSymbolRequest: attributes method called');
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
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        Log::info('SppSymbolRequest: rules method called');
        return [
            'fund' => 'required|string',
            'spp_symbol' => 'required|string|unique:spp_symbols,spp_symbol',
            'functional_region' => 'required|string',
            'account' => 'required|string',
            'financial_centre' => 'required|string',
            'grantee' => 'required|string|max:200',
        ];
    }
}
