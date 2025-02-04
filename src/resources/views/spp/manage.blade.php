@php
$old_spp = old("spp");
$isEditMode = isset($editing_spp);
@endphp

<x-layout>
    <x-content-box title="ŠPP prvky">
        {{-- Deactivation Form --}}
        <div>
            <form action="{{ route('spp.deactivate') }}" method="POST">
                @csrf
                @method('PUT')
                <x-content-section title="Deaktivácia">
                    <div class="form-row">
                        <div class="col-md-6 col-12">
                            <x-dropdown-input name="spp" :values="$spp_symbols" label="ŠPP prvok:" :selected="$old_spp"></x-dropdown-input>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="d-flex justify-content-md-start justify-content-end h-100">
                                <div class="form-group align-self-end">
                                    <x-button color="danger">Deaktivovať</x-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-content-section>
            </form>
        </div>

        {{-- Activation Form --}}
        <div>
            <form action="{{ route('spp.activate') }}" method="POST">
                @csrf
                @method('PUT')
                <x-content-section title="Aktivácia">
                    <div class="form-row">
                        <div class="col-md-6 col-12">
                            <x-dropdown-input name="spp" :values="$deactivated_symbols" label="ŠPP prvok:"></x-dropdown-input>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="d-flex justify-content-md-start justify-content-end h-100">
                                <div class="form-group align-self-end">
                                    <x-button color="danger">Aktivovať</x-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-content-section>
            </form>
        </div>

        {{-- New or Edit Form --}}
        <form action="{{ $isEditMode ? route('spp.update', $editing_spp->id) : route('spp.store') }}" method="POST">
            @csrf
            @if($isEditMode) @method('PUT') @endif
            <x-content-section title="{{ $isEditMode ? 'Upraviť ŠPP prvok' : 'Pridať ŠPP prvok' }}">
                {{-- Select Existing SPP Symbol --}}
                <div class="form-row mb-3">
                    <div class="col-md-6 col-12">
                        <label for="existing_spp">Výber ŠPP prvku:</label>
                        <select class="form-control" id="existing_spp" onchange="location = this.value;">
                            <option value="{{ route('spp.manage') }}" {{ !$isEditMode ? 'selected' : '' }}>
                            Nový prvok
                            </option>
                            @foreach($spp_symbols as $id => $symbol)
                            <option value="{{ route('spp.edit', $id) }}"
                                    {{ $isEditMode && $editing_spp->id == $id ? 'selected' : '' }}>
                                {{ $symbol }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Form Fields --}}
                <div class="form-row">
                    <div class="col-md-6 col-12">
                        <x-simple-input name="spp_symbol" label="ŠPP prvok:"
                                        value="{{ $isEditMode ? $editing_spp->spp_symbol : old('spp_symbol') }}"></x-simple-input>
                    </div>
                    <div class="col-md-6 col-12">
                        <x-simple-input name="functional_region" label="Funkčná oblasť:"
                                        value="{{ $isEditMode ? $editing_spp->functional_region : old('functional_region') }}"></x-simple-input>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 col-12">
                        <x-simple-input name="financial_centre" label="Finančné stredisko:"
                                        value="{{ $isEditMode ? $editing_spp->financial_centre : old('financial_centre') }}"></x-simple-input>
                    </div>
                    <div class="col-md-6 col-12">
                        <label for="grantee">Zodpovedný riešiteľ:</label>
                        <select class="form-control" id="grantee" name="grantee">
                            @foreach($all_users as $user)
                            <option value="{{ $user->id }}"
                                    {{ $isEditMode && $editing_spp->grantee == $user->id ? 'selected' : '' }}>
                                {{ $user->first_name . ' ' . $user->last_name }}
                                @if($user->academic_degrees)
                                ({{ $user->academic_degrees }})
                                @endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 col-12">
                        <x-simple-input name="agency" label="Agentúra:"
                                        value="{{ $isEditMode ? $editing_spp->agency : old('agency') }}"></x-simple-input>
                    </div>
                    <div class="col-md-6 col-12">
                        <x-simple-input name="acronym" label="Akronym projektu:"
                                        value="{{ $isEditMode ? $editing_spp->acronym : old('acronym') }}"></x-simple-input>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="d-flex justify-content-end">
                    <x-button>{{ $isEditMode ? 'Uložiť zmeny' : 'Pridať ŠPP prvok' }}</x-button>
                </div>
            </x-content-section>
        </form>
    </x-content-box>
</x-layout>
