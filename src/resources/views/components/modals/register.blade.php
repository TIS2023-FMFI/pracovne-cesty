<x-modal title="Registrácia" event="open-register-form" control="registerOpen">
    <form action="users/store">
        @csrf
        <x-content-section>
            <x-simple-input name="first_name" label="Meno:" size="long"></x-simple-input>
            <x-simple-input name="last_name" label="Priezvisko:" size="long"></x-simple-input>
            <x-simple-input name="email" label="E-mail:" size="long"></x-simple-input>
            <x-simple-input name="username" label="Prihlasovacie meno:" size="long"></x-simple-input>
            <x-simple-input name="password" label="Heslo:" size="long"></x-simple-input>
        </x-content-section>
        <div class="d-flex justify-content-end">
            <x-button>Zaregistrovať sa</x-button>
        </div>
    </form>
</x-modal>
