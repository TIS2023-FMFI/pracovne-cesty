<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'max' => 'Pole :attribute môže mať maximálne :max znakov.',
            'email' => 'Pole :attribute musí byť platná e-mailová adresa.',
            'unique' => 'Pole :attribute už bolo zaregistrované.',
            'confirmed' => 'Potvrdenie :attribute sa nezhoduje.',
            'min' => 'Pole :attribute musí obsahovať aspoň :min znakov.',
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
            'email' => 'e-mail',
            'password' => 'heslo',
        ];
    }
}
