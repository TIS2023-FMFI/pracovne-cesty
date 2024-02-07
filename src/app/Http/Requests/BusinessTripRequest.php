<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BusinessTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'academic_degrees' => 'nullable|string|max:30',
            'department' => 'required|string|max:10',
            'address' => 'nullable|string|max:200',
            'country_id' => 'required|exists:countries,id',
            'transport_id' => 'required|exists:transports,id',
            'spp_symbol_id' => 'required|exists:spp_symbols,id',
            'place' => 'required|string|max:200',
            'place_start' => 'required|string|max:200',
            'place_end' => 'required|string|max:200',
            'datetime_start' => 'required|date',
            'datetime_end' => 'required|date|after:datetime_start',
            'trip_purpose_id' => 'required|exists:trip_purposes,id',
            'purpose_details' => 'nullable|string|max:50',
            'iban' => 'nullable|string|max:34',
            'contribution_{id}_detail => nullable|string|max:200',
            'reimbursement_spp_symbol_id' => 'required|exists:spp_symbols,id',
            'reimbursement_date' => 'required|date',
            'organiser_name' => 'required|string|max:100',
            'ico' => 'nullable|string|max:8',
            'organiser_address' => 'required|string|max:200',
            'organiser_iban' => 'required|string|max:34',
            'amount' => 'required|string|max:20',
        ];
    }
    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'first_name.required' => 'Meno je povinné pole.',
            'last_name.required' => 'Priezvisko je povinné pole.',
            'academic_degrees.nullable' => 'Akademický titul nie je povinný.',
            'department.required' => 'Útvar je povinné pole.',
            'address.max' => 'Adresa môže mať maximálne 200 znakov.',
            'country_id.required' => 'Krajina je povinné pole.',
            'country_id.exists' => 'Vybraná krajina neexistuje.',
            'transport_id.required' => 'Doprava je povinné pole.',
            'transport_id.exists' => 'Vybraný spôsob dopravy neexistuje.',
            'spp_symbol_id.required' => 'Symbol SPP je povinné pole.',
            'spp_symbol_id.exists' => 'Vybraný symbol SPP neexistuje.',
            'place.required' => 'Miesto je povinné pole.',
            'place.string' => 'Miesto musí byť reťazec.',
            'place.max' => 'Miesto môže mať maximálne 200 znakov.',
            'place_start.required' => 'Miesto začiatku je povinné pole.',
            'place_start.string' => 'Miesto začiatku musí byť reťazec.',
            'place_start.max' => 'Miesto začiatku môže mať maximálne 200 znakov.',
            'place_end.required' => 'Miesto konca je povinné pole.',
            'place_end.string' => 'Miesto konca musí byť reťazec.',
            'place_end.max' => 'Miesto konca môže mať maximálne 200 znakov.',
            'datetime_start.required' => 'Dátum začiatku je povinné pole.',
            'datetime_start.date' => 'Dátum začiatku musí byť platný dátum.',
            'datetime_end.required' => 'Dátum konca je povinné pole.',
            'datetime_end.date' => 'Dátum konca musí byť platný dátum.',
            'datetime_end.after' => 'Dátum konca musí byť po dátume začiatku.',
            'trip_purpose_id.required' => 'Účel cesty je povinný.',
            'trip_purpose_id.exists' => 'Zvolený účel cesty nie je platný.',
            'purpose_details.nullable' => 'Detaily účelu sú voliteľné.',
            'purpose_details.string' => 'Detaily účelu musia byť reťazec.',
            'purpose_details.max' => 'Detaily účelu môžu mať maximálne 50 znakov.',
            'iban.nullable' => 'IBAN je voliteľný.',
            'iban.string' => 'IBAN musí byť reťazec.',
            'iban.max' => 'IBAN môže mať maximálne 34 znakov.',
            'reimbursement_spp_symbol_id.required' => 'Symbol SPP pre náhradu je povinné pole.',
            'reimbursement_spp_symbol_id.exists' => 'Vybraný symbol SPP pre náhradu neexistuje.',
            'reimbursement_date.required' => 'Dátum náhrady je povinné pole.',
            'reimbursement_date.date' => 'Dátum náhrady musí byť platný dátum.',
            'organiser_name.required' => 'Meno organizátora je povinné pole.',
            'organiser_name.string' => 'Meno organizátora musí byť reťazec.',
            'organiser_name.max' => 'Meno organizátora môže mať maximálne 100 znakov.',
            'ico.nullable' => 'IČO je voliteľné.',
            'ico.string' => 'IČO musí byť reťazec.',
            'ico.max' => 'IČO môže mať maximálne 8 znakov.',
            'organiser_address.required' => 'Adresa organizátora je povinné pole.',
            'organiser_address.string' => 'Adresa organizátora musí byť reťazec.',
            'organiser_address.max' => 'Adresa organizátora môže mať maximálne 200 znakov.',
            'organiser_iban.required' => 'IBAN organizátora je povinné pole.',
            'organiser_iban.string' => 'IBAN organizátora musí byť reťazec.',
            'organiser_iban.max' => 'IBAN organizátora môže mať maximálne 34 znakov.',
            'amount.required' => 'Suma je povinné pole.',
            'amount.string' => 'Suma musí byť reťazec.',
            'amount.max' => 'Suma môže mať maximálne 20 znakov.',
        ];
    }

}
