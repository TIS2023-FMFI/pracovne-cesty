<x-modal title="Registrácia" event="open-register-form" control="registerOpen">
    <form action="users/store">
        @csrf
        <x-content-section>
            <div class="form-row">
                <div class="col">
                    <x-simple-input name="first_name" label="Meno:" size="long"></x-simple-input>
                </div>
                <div class="col">
                    <x-simple-input name="last_name" label="Priezvisko:" size="long"></x-simple-input>
                </div>
            </div>
            <x-simple-input name="email" label="E-mail:" size="long"></x-simple-input>
            <div class="form-row">
                <div class="col">
                    <x-simple-input name="username" label="Prihlasovacie meno:" size="long"></x-simple-input>
                </div>
                <div class="col">
                    <x-simple-input name="password" label="Heslo:" size="long"></x-simple-input>
                </div>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="user_types" id="type1" value="externist">
                <label class="form-check-label" for="type1">Externista</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="user_types" id="type2" value="student">
                <label class="form-check-label" for="type2">Študent</label>
            </div>
        </x-content-section>
        <div class="d-flex justify-content-end">
            <x-button>Zaregistrovať sa</x-button>
        </div>
    </form>
</x-modal>
