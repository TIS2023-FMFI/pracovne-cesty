<x-modal title="Pridať používateľov" name="add-users">
    <form action="{{ route('user.invite') }}" method="POST">
        @csrf
        <x-content-section>
            <p>Môžete zadať ľubovoľný počet emailových adries <b>oddelených bodkočiarkami</b>.</p>
            <p>Na tieto emailové adresy bude odoslaný link, pomocou ktorého sa pozvaní používatelia môžu do Pracovných ciest zaregistrovať.</p>
            <x-textarea name="email" label="Emailové adresy"></x-textarea>
        </x-content-section>
        <div class="d-flex justify-content-end">
            <x-button>Pridať</x-button>
        </div>
    </form>
</x-modal>
