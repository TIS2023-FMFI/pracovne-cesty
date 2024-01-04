@php
    use App\Models\SppSymbol;
    $spp_symbols = SppSymbol::all()->pluck('spp_symbol', 'id')->toArray();
@endphp

<x-modal title="ŠPP prvky" event="open-spp-manager" control="sppOpen">
    <form action="spp/deactivate">
        @csrf
        <x-content-section title="Deaktivácia">
            <div class="form-row">
                <div class="col">
                    <x-dropdown-input name="spp_symbol" :values="$spp_symbols" label="ŠPP prvok:"></x-dropdown-input>
                </div>
                <div class="col align-bottom">
                    <x-button>Deaktivovať</x-button>
                </div>
            </div>
        </x-content-section>
        <div class="d-flex justify-content-end">

        </div>

    </form>
    <form action="spp/store">
        @csrf
        <x-content-section title="Nový ŠPP prvok">
            <div class="form-row">
                <div class="col">
                    <x-simple-input name="fund" label="Fond:"></x-simple-input>
                </div>
                <div class="col">
                    <x-simple-input name="spp_symbol" label="ŠPP prvok:"></x-simple-input>
                </div>

            </div>
            <div class="form-row">
                <div class="col">
                    <x-simple-input name="functional_region" label="Funkčná oblasť:"></x-simple-input>
                </div>
                <div class="col">
                    <x-simple-input name="account" label="Účet:"></x-simple-input>
                </div>
            </div>
            <div class="form-row">
                <div class="col">
                    <x-simple-input name="financial_centre" label="Finančné stredisko:"></x-simple-input>
                </div>
                <div class="col">
                    <x-simple-input name="grantee" label="Zodpovedný riešiteľ"></x-simple-input>
                </div>
            </div>
        </x-content-section>
        <div class="d-flex justify-content-end">
            <x-button>Pridať</x-button>
        </div>

    </form>
</x-modal>
