<x-modal title="Pridať použivateľov" event="open-add-users" control="usersOpen">
    <form action="users/invite">
        @csrf
        <x-content-section>
            <x-slot:description>
                Môžete zadať ľubovoľný počet emailových adries oddelených bodkočiarkami.
            </x-slot:description>
            <x-textarea name="email" label="Emailové adresy:" size="long"></x-textarea>
        </x-content-section>
        <div class="d-flex justify-content-end">
            <x-button>Pridať</x-button>
        </div>
    </form>
</x-modal>
