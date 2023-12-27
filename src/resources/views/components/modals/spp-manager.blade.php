<x-modal title="ŠPP prvky" event="open-spp-manager" control="sppOpen">
    <form action="spp/add">
        @csrf
        <x-content-section>
            <x-simple-input name="email" label="E-mail:"></x-simple-input>
        </x-content-section>
        <x-button>Pridať používateľa</x-button>
    </form>
    <form action="spp/deactivate">
        @csrf
        <x-content-section>
            <x-simple-input name="email" label="E-mail:"></x-simple-input>
        </x-content-section>
        <x-button>Pridať používateľa</x-button>
    </form>
</x-modal>
