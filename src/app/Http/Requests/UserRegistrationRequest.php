<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Enums\UserType;


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
        Log::info('UserRegistrationRequest: messages method called');
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
        Log::info('UserRegistrationRequest: attributes method called');
        return [
            'first_name' => 'meno',
            'last_name' => 'priezvisko',
            'email' => 'e-mail',
            'password' => 'heslo',
            'username' => 'prihlasovacie meno',
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        Log::info('UserRegistrationRequest: rules method called');
        $validUserTypes = implode(',', [UserType::EXTERN->value, UserType::STUDENT->value]);

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'username' => 'required|string|max:255',
            'user_types' => 'required|in:' . $validUserTypes,
        ];
    }
}
