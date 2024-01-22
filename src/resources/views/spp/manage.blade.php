@php
    use App\Models\SppSymbol;
    use App\Enums\SppStatus;

    $spp_symbols = SppSymbol::where('status', SppStatus::ACTIVE)->pluck('spp_symbol', 'id');
@endphp

<x-layout>
    <x-content-box title="ŠPP prvky">
        <div x-data="{ sppId: '' }">
            <form x-bind:action="'spp/' + sppId + '/deactivate'" method="POST">
                @csrf
                <x-content-section title="Deaktivácia">
                    <div class="form-row">
                        <div class="col">
                            <x-dropdown-input control="sppId" name="spp_symbol" :values="$spp_symbols" label="ŠPP prvok:"></x-dropdown-input>
                        </div>
                        <div class="col">
                            <x-button color="danger">Deaktivovať</x-button>
                        </div>
                    </div>
                </x-content-section>
            </form>
        </div>

        <form action="spp/">
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

                <div class="d-flex justify-content-end">
                    <x-button>Pridať ŠPP prvok</x-button>
                </div>
            </x-content-section>


        </form>
    </x-content-box>
</x-layout>
