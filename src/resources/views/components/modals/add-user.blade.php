<x-modal title="Pridať použivateľov" event="open-add-users" control="usersOpen">
    <form action="users/add">
        @csrf
        <x-content-section>
            <x-simple-input name="email" label="E-mail:"></x-simple-input>
        </x-content-section>
        <x-form-button>Pridať používateľa</x-form-button>
    </form>
</x-modal>
