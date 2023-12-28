@php
    use App\Models\Country;
    use App\Models\Transport;
    use App\Models\TripPurpose;
    use App\Models\Contribution;
    use App\Models\SppSymbol;;
    $countries = Country::all()->pluck('name', 'id')->toArray();
    $transports = Transport::all()->pluck('name', 'id')->toArray();
    $purposes = TripPurpose::all()->pluck('name', 'id')->toArray();;
    $contributions = Contribution::all()->pluck('name', 'id')->toArray();
    $spp_symbols = SppSymbol::all()->pluck('spp_symbol', 'id')->toArray();;
@endphp

<x-layout>
    <x-content-box title="Nová pracovná cesta">
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

            <x-content-section title="Začiatok cesty">
                <x-simple-input name="place_start" label="Miesto:"/>
                <x-simple-input name="datetime_start" type="datetime-local" label="Dátum a čas:"/>
            </x-content-section>

            <x-content-section title="Koniec cesty">
                <x-simple-input name="place_end" label="Miesto:"/>
                <x-simple-input name="datetime_end" type="datetime-local" label="Dátum a čas:"/>
            </x-content-section>

            <x-content-section title="Cieľ cesty">
                <x-simple-input name="place" label="Miesto:"/>
                <x-dropdown-input name="country" label="Štát:" :values="$countries" selected="{{old('country')}}"/>
                <x-dropdown-input name="transport" label="Dopravný prostriedok:" :values="$transports" selected="{{old('transport')}}"/>
                <x-simple-input name="upload_name" type="file" label="Vložte pozvánku, plagát alebo iný súbor..."/>
                <x-dropdown-input name="trip_purpose" label="Účel cesty:" :values="$purposes" selected="{{old('purpose')}}"/>
                <x-textarea name="purpose_details" label="Špecifikácia účelu:"></x-textarea>
                <x-simple-input name="event_url" label="Link na udalosť:"/>
            </x-content-section>

            <x-content-section title="Prínos pre fakultu">
                @foreach($contributions as $id => $name)
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input type="checkbox">
                            </div>
                            <span class="input-group-text">{{ $name }}</span>
                        </div>
                        <input type="text" name="contribution_{{ $id }}" class="form-control">
                    </div>
                @endforeach

            </x-content-section>

            <x-content-section title="Financovanie" x-data="{reimbursementShow: false}">
                <x-slot:description>
                    V prípade refundácie, prosím, vyberte ako ŠPP prvok 2 ten prvok, z ktorého budú peniaze neskôr vrátené do ŠPP prvku 1. Ako dátum vrátenia peňazí uveďte iba orientačný, predpokladaný dátum.
                </x-slot:description>
                <x-dropdown-input name="spp_symbol" label="ŠPP prvok 1:" :values="$spp_symbols" selected="{{old('spp_symbol')}}"/>
                <x-checkbox name="reimbursement" label="Refundovať" control="reimbursementShow"></x-checkbox>
                <div x-show="reimbursementShow" class="col-md-12 row">
                    <x-dropdown-input name="reimbursement_spp_symbol" label="ŠPP prvok 2:" :values="$spp_symbols" selected="{{old('reimbursement_spp')}}"/>
                    <x-simple-input name="reimbursement_date" type="date" label="Predpokladaný dátum:"/>
                </div>

            </x-content-section>

            <x-content-section title="Úhrada konferenčného poplatku" x-data="{conferenceFeeShow: false}">
                <x-checkbox name="conference_fee" label="Mám záujem o úhradu konferenčného poplatku pred cestou priamo z pracoviska" control="conferenceFeeShow"></x-checkbox>
                <div x-show="conferenceFeeShow" class="col-md-12 row">
                    <x-simple-input name="organiser_name" type="text" label="Názov organizácie:"/>
                    <x-simple-input name="ico" type="text" label="IČO:"/>
                    <x-simple-input name="organiser_address" type="text" label="Adresa organizácie:"/>
                    <x-simple-input name="organiser_iban" type="text" label="Číslo účtu organizácie:"/>
                    <x-simple-input name="amount" type="text" label="Suma:"/>
                </div>
            </x-content-section>

            <div class="container">
                <x-button>Uložiť úpravy</x-button>
            </div>

        </form>

    </x-content-box>
</x-layout>
