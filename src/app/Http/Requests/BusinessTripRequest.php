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
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required' => 'Pole :attribute je povinné.',
            'string' => 'Pole :attribute musí byť reťazec.',
            'max' => [
                'string' => 'Pole :attribute môže mať maximálne :max znakov.',
                'numeric' => 'Pole :attribute nesmie byť väčšie ako :max.',
            ],
            'date' => 'Pole :attribute musí byť platný dátum.',
            'after' => 'Pole :attribute musí byť dátum po :date.',
            'exists' => 'Vybrané pole :attribute je neplatné.',
            'unique' => 'Pole :attribute už bolo zaregistrované.',
            'email' => 'Pole :attribute musí byť platná e-mailová adresa.',
            'confirmed' => 'Potvrdenie :attribute sa nezhoduje.',
            'integer' => 'Pole :attribute musí byť celé číslo.',
            'min' => [
                'numeric' => 'Pole :attribute musí byť aspoň :min.',
                'string' => 'Pole :attribute musí obsahovať aspoň :min znakov.',
            ],
            'nullable' => 'Pole :attribute môže byť prázdne.',
        ];
    }
    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'first_name' => 'meno',
            'last_name' => 'priezvisko',
            'academic_degrees' => 'akademický titul',
            'department' => 'útvar',
            'address' => 'adresa',
            'country_id' => 'krajina',
            'transport_id' => 'doprava',
            'spp_symbol_id' => 'symbol SPP',
            'place' => 'miesto',
            'place_start' => 'miesto začiatku',
            'place_end' => 'miesto konca',
            'datetime_start' => 'dátum začiatku',
            'datetime_end' => 'dátum konca',
            'trip_purpose_id' => 'účel cesty',
            'purpose_details' => 'detaily účelu',
            'iban' => 'IBAN',
            'reimbursement_spp_symbol_id' => 'symbol SPP pre náhradu',
            'reimbursement_date' => 'dátum náhrady',
            'organiser_name' => 'názov organizácie',
            'ico' => 'IČO',
            'organiser_address' => 'adresa organizátora',
            'organiser_iban' => 'IBAN organizátora',
            'amount' => 'suma',
            'contribution_{id}' => 'príspevok',
            'reimbursement' => 'náhrada',
            'conference_fee' => 'konferenčný poplatok',
            'travelling_expense_eur' => 'cestovné náklady v EUR',
            'accommodation_expense_eur' => 'náklady na ubytovanie v EUR',
            'allowance_expense_eur' => 'príspevok v EUR',
            'advance_expense_eur' => 'záloha v EUR',
            'other_expense_eur' => 'ostatné náklady v EUR',
            'travelling_expense_foreign' => 'cestovné náklady v cudzej mene',
            'accommodation_expense_foreign' => 'náklady na ubytovanie v cudzej mene',
            'allowance_expense_foreign' => 'príspevok v cudzej mene',
            'advance_expense_foreign' => 'záloha v cudzej mene',
            'other_expense_foreign' => 'ostatné náklady v cudzej mene',
            'reimburse' => 'náhrada',
        ];
    }

}
