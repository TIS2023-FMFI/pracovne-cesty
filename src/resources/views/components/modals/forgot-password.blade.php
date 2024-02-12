<x-modal title="Zabudnuté heslo" name="forgot-password">
    <form action="/forgot-password" method="POST">
        @csrf
        <x-content-section>
            <x-slot:description>
                <p>Zabudli ste heslo?</p>
                <p>Zadajte svoj email, bude vám zaslaný link, kde si môžete nastaviť nové heslo.</p>
            </x-slot:description>
            <x-simple-input name="email" label="Emailová adresa"></x-simple-input>
        </x-content-section>
        <div class="d-flex justify-content-end">
            <x-button>Odoslať</x-button>
        </div>
    </form>
</x-modal>
