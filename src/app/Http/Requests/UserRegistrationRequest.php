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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
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
            'email.required' => 'E-mail je povinné pole.',
            'email.email' => 'E-mail musí byť platná e-mailová adresa.',
            'email.unique' => 'E-mail už je zaregistrovaný.',
            'password.required' => 'Heslo je povinné pole.',
            'password.min' => 'Heslo musí mať aspoň 6 znakov.',
            'password.confirmed' => 'Heslá sa nezhodujú.',
        ];
    }
}
