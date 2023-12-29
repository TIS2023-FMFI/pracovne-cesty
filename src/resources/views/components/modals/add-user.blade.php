<x-modal title="Pridať použivateľov" event="open-add-users" control="usersOpen">
    <form action="users/add">
        @csrf
        <x-content-section>
            <x-simple-input name="email" label="E-mail:" size="long"></x-simple-input>
        </x-content-section>
        <x-button>Pridať používateľa</x-button>
    </form>
</x-modal>
