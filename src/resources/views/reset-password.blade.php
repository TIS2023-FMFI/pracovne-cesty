<x-layout>
    <x-content-box title="Zmena hesla">
        <form action="/reset-password" method="POST">
            @csrf
            <x-content-section>
                <div class="form-row">
                    <div class="col">
                        <x-simple-input type="password" name="password" label="Nové heslo"/>
                    </div>
                    <div class="col">
                        <x-simple-input type="password" name="password_confirmation" label="Zopakovať heslo"/>
                    </div>
                </div>
                <x-simple-input name="token" :value="$token" hidden/>
            </x-content-section>
            <div class="d-flex justify-content-end">
                <x-button>Zmeniť heslo</x-button>
            </div>
        </form>
    </x-content-box>
</x-layout>
