@php
    $countries = ['Slovensko', 'Česko', 'Rakúsko'];
    $transports = ['auto', 'lietadlo', 'vlak'];
    $purposes = ['konferencia', 'výskum', 'stretnutie'];
    $contributions = ['prínos1', 'prínos2'];
    $spp = ['spp1', 'spp2'];

@endphp

<x-layout>
    <x-content-box title="Úprava pracovnej cesty">
        <form method="POST" action="/trips/{{ $trip->id }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
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
                <x-simple-input name="place start" label="Miesto:"></x-simple-input>
                <x-simple-input name="datetime_start" type="datetime-local" label="Dátum a čas:" :value="$trip->start_location"/>
                <x-simple-input name="datetime_border_crossing_start" type="datetime-local" label="Dátum a čas prekročenia hraníc:"/>
            </x-content-section>

            <x-content-section title="Koniec cesty">
                <x-simple-input name="place_end" label="Miesto:" :value="$trip->end_location"/>
                <x-simple-input name="datetime_end" type="datetime-local" label="Dátum a čas:" :value="$trip->start_location"/>
                <x-simple-input name="datetime_border_crossing_end" type="datetime-local" label="Dátum a čas prekročenia hraníc:"/>
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

            <x-content-section title="Financovanie" x-data="{reimbursementShow: false}">
                <x-slot:description>
                    V prípade refundácie, prosím, vyberte ako ŠPP prvok 2 ten prvok, z ktorého budú peniaze neskôr vrátené do ŠPP prvku 1. Ako dátum vrátenia peňazí uveďte iba orientačný, predpokladaný dátum.
                </x-slot:description>
                <x-dropdown-input name="spp_symbol" label="ŠPP prvok 1:" :values="$spp" selected="{{old('spp_symbol')}}"/>
                <x-checkbox name="reimbursement" label="Refundovať" control="reimbursementShow"></x-checkbox>
                <div x-show="reimbursementShow" class="col-md-12 row mt-3">
                    <x-dropdown-input name="reimbursement_spp" label="ŠPP prvok 2:" :values="$spp" selected="{{old('reimbursement_spp')}}"/>
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

            <x-content-section title="Náklady">
                <div class="container mt-4">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Dátum</th>
                                <th>Raňajky</th>
                                <th>Obed</th>
                                <th>Večera</th>
                            </tr>
                        </thead>
                        <tbody x-data="{checkBreakfast: false, checkLunch: false, checkDinner: false}">
                        <tr>
                            <td>Všetky</td>
                            <td><input type="checkbox" x-model="checkBreakfast"></td>
                            <td><input type="checkbox" x-model="checkLunch"></td>
                            <td><input type="checkbox" x-model="checkDinner"></td>
                        </tr>

                        @for ($i = 0; $i < 10; $i++)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>
                                    <input type="checkbox" x-bind:checked="checkBreakfast">
                                </td>
                                <td>
                                    <input type="checkbox" x-bind:checked="checkLunch">
                                </td>
                                <td>
                                    <input type="checkbox" x-bind:checked="checkDinner">
                                </td>
                            </tr>
                        @endfor
                        </tbody>
                    </table>
                </div>


            </x-content-section>

            <x-content-section title="Správa">
                <textarea id="conclusion" name="conclusion" rows="5" cols="50"></textarea>
            </x-content-section>

            <div>
                <x-form-button>Uložiť úpravy</x-form-button>
            </div>

        </form>

    </x-content-box>
</x-layout>
