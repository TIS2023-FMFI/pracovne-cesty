@php
    $countries = ['Slovensko', 'Česko', 'Rakúsko'];
    $transports = ['auto', 'lietadlo', 'vlak'];
    $purposes = ['konferencia', 'výskum', 'stretnutie'];
    $contributions = ['prínos1', 'prínos2'];
    $spp = ['spp1', 'spp2'];

@endphp

<x-layout>
    <x-content-box title="Nová pracovná cesta" style="width: 70%">
        <form method="POST" action="/trips" enctype="multipart/form-data">
            @csrf
            <x-content-section title="Osobné údaje">
                <x-simple-input name="first_name" label="Meno:"/>
                <x-simple-input name="last_name" label="Priezvisko:"/>
                <x-simple-input name="academic_degrees" label="Tituly:"/>
                <x-simple-input name="address" label="Bydlisko:"/>
                <x-simple-input name="personal_id" label="Osobné číslo:"/>
                <x-simple-input name="department" label="Pracovisko:"/>
                <x-simple-input name="iban" label="Číslo účtu:"/>
            </x-content-section>

            <x-content-section title="">
                <div>
                    <p>Začiatok cesty</p>
                    <x-simple-input name="place start" label="Miesto:"></x-simple-input>
                    <x-simple-input name="datetime_start" type="datetime-local" label="Dátum a čas:"/>
                </div>
                <div>
                    <p>Koniec cesty</p>
                    <x-simple-input name="place_end" label="Miesto:"/>
                    <x-simple-input name="datetime_end" type="datetime-local" label="Dátum a čas:"/>
                </div>
            </x-content-section>

            <x-content-section title="Cieľ cesty">
                <x-simple-input name="place" label="Miesto:"/>
                <x-dropdown-input name="country" label="Štát:" :values="$countries" selected="{{old('country')}}"/>
                <x-dropdown-input name="transport" label="Dopravný prostriedok:" :values="$transports" selected="{{old('transport')}}"/>
                <x-simple-input name="upload_name" type="file" label="Vložte pozvánku, plagát alebo iný súbor..."/>
                <x-dropdown-input name="trip_purpose" label="Účel cesty:" :values="$purposes" selected="{{old('purpose')}}"/>
                <textarea id="purpose_details" name="purpose_details" rows="4" cols="30"></textarea>
                <x-simple-input name="event_url" label="Link na udalosť:"/>
                <x-dropdown-input name="contribution" label="Prínos pre fakultu:" :values="$contributions" selected="{{old('contribution')}}"/>
            </x-content-section>

            <x-content-section x-data="{reimbursementShow: false}">
                <p>V prípade refundácie, prosím, vyberte ako ŠPP prvok 2 ten prvok, z ktorého budú peniaze neskôr vrátené do ŠPP prvku 1. Ako dátum vrátenia peňazí uveďte iba orientačný, predpokladaný dátum.</p>
                <x-dropdown-input name="spp_symbol" label="ŠPP prvok 1:" :values="$spp" selected="{{old('spp_symbol')}}"/>
                <x-checkbox name="reimbursement" label="Refundovať" control="reimbursementShow"></x-checkbox>
                <div x-show="reimbursementShow">
                    <x-dropdown-input name="spp2" label="ŠPP prvok 2:" :values="$spp" selected="{{old('spp2')}}"/>
                    <x-simple-input name="reimbursement_date" type="date" label="Predpokladaný dátum:"/>
                </div>

            </x-content-section>

            <x-content-section x-data="{conferenceFeeShow: false}">
                <x-checkbox name="conference_fee" label="Mám záujem o úhradu konferenčného poplatku pred cestou priamo z pracoviska" control="conferenceFeeShow"></x-checkbox>
                <div x-show="conferenceFeeShow">
                    <x-simple-input name="organiser_name" type="text" label="Názov organizácie:"/>
                    <x-simple-input name="ico" type="text" label="IČO:"/>
                    <x-simple-input name="organiser_address" type="text" label="Adresa organizácie:"/>
                    <x-simple-input name="organiser_iban" type="text" label="Číslo účtu organizácie:"/>
                    <x-simple-input name="amount" type="text" label="Suma:"/>
                </div>
            </x-content-section>

            <div>
                <x-form-button>Uložiť úpravy</x-form-button>
            </div>

        </form>

    </x-content-box>
</x-layout>
