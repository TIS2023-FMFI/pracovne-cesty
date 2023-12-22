<x-modal title="ŠPP prvky" event="open-spp-manager" control="sppOpen">
    <form action="spp/add">
        @csrf
        <x-simple-input name="email" label="E-mail:"></x-simple-input>
        <x-form-button>Pridať používateľa</x-form-button>
    </form>
    <form action="spp/deactivate">
        @csrf
        <x-simple-input name="email" label="E-mail:"></x-simple-input>
        <x-form-button>Pridať používateľa</x-form-button>
    </form>
</x-modal>
