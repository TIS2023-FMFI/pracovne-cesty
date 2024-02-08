@php
use App\Enums\UserType;
@endphp



<x-layout>
    <x-content-box title="Registrácia">
        <form action="/user/register/store" method="POST">
            @csrf
            <x-content-section>
                <div class="form-row">
                    <div class="col">
                        <x-simple-input name="first_name" label="Meno:"></x-simple-input>
                    </div>
                    <div class="col">
                        <x-simple-input name="last_name" label="Priezvisko:"></x-simple-input>
                    </div>
                </div>
                <x-simple-input name="email" label="E-mail:" :value="$email" :readonly="true"></x-simple-input>
                <div class="form-row">
                    <div class="col">
                        <x-simple-input name="username" label="Prihlasovacie meno:"></x-simple-input>
                    </div>
                    <div class="col">
                        <x-simple-input type="password" name="password" label="Heslo:"></x-simple-input>
                    </div>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="user_types" id="type1" value="{{UserType::EXTERN->value}}">
                    <label class="form-check-label" for="type1">Externista</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="user_types" id="type2" value="{{UserType::STUDENT->value}}">
                    <label class="form-check-label" for="type2">Študent</label>
                </div>
            </x-content-section>
            <div class="d-flex justify-content-end">
                <x-button>Zaregistrovať sa</x-button>
            </div>
        </form>
    </x-content-box>
</x-layout>
