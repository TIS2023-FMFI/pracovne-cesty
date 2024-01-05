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
                <div class="form-row">
                    <div class="col">
                        <x-simple-input name="first_name" label="Meno"/>
                    </div>
                    <div class="col">
                        <x-simple-input name="last_name" label="Priezvisko"/>
                    </div>
                    <div class="col">
                        <x-simple-input name="academic_degrees" label="Tituly"/>
                    </div>
                </div>

                <x-simple-input name="address" label="Bydlisko"/>

                <div class="form-row">
                    <div class="col">
                        <x-simple-input name="personal_id" label="Osobné číslo"/>
                    </div>
                    <div class="col">
                        <x-simple-input name="department" label="Pracovisko"/>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-6">
                        <x-simple-input name="iban" label="Číslo účtu"/>
                    </div>
                </div>
            </x-content-section>


            <div class="form-row">
                <div class="col">
                    <x-content-section title="Začiatok cesty">
                        <x-simple-input name="place start" label="Miesto"/>
                        <x-simple-input name="datetime_start" type="datetime-local" label="Dátum a čas"/>
                    </x-content-section>
                </div>
                <div class="col">
                    <x-content-section title="Koniec cesty">
                        <x-simple-input name="place_end" label="Miesto"/>
                        <x-simple-input name="datetime_end" type="datetime-local" label="Dátum a čas"/>
                    </x-content-section>
                </div>
            </div>

                <x-content-section title="Cieľ cesty">
                    <div class="form-row">
                        <div class="col">
                            <x-simple-input name="place" label="Miesto"/>
                        </div>
                        <div class="col">
                            <x-dropdown-input name="country" label="Štát" :values="$countries"/>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <x-dropdown-input name="trip_purpose" label="Účel cesty" :values="$purposes"/>
                        </div>
                        <div class="col">
                            <x-textarea name="purpose_details" label="Špecifikácia účelu"></x-textarea>
                        </div>
                    </div>
{{--                        <div class="form-group">--}}
{{--                            <label for="purpose">Účel cesty</label>--}}
{{--                            <div class="input-group mb-3">--}}
{{--                                <select class="custom-select col-4" id="purpose">--}}
{{--                                    <option selected>Select Option</option>--}}
{{--                                    <option value="1">Option 1</option>--}}
{{--                                    <option value="2">Option 2</option>--}}
{{--                                    <option value="3">Option 3</option>--}}
{{--                                </select>--}}
{{--                                <input type="text" class="form-control" placeholder="môžete špecifikovať">--}}
{{--                            </div>--}}
{{--                        </div>--}}

                    <div class="form-row">
                        <div class="col">
                            <x-simple-input name="upload_name" type="file" label="Vložte pozvánku, plagát alebo iný súbor..."/>
                        </div>
                        <div class="col">
                            <x-simple-input name="event_url" label="Link na udalosť"/>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-sm-6">
                            <x-dropdown-input name="transport" label="Dopravný prostriedok" :values="$transports"/>
                        </div>
                    </div>
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
                        <input type="text" name="contribution_{{ $id }}_detail" class="form-control">
                    </div>
                @endforeach

            </x-content-section>

            <x-content-section title="Financovanie" x-data="{reimbursementShow: false}">
                <x-slot:description>
                    V prípade refundácie, prosím, vyberte ako ŠPP prvok 2 ten prvok, z ktorého budú peniaze neskôr vrátené do ŠPP prvku 1. Ako dátum vrátenia peňazí uveďte iba orientačný, predpokladaný dátum.
                </x-slot:description>
                <div class="form-row align-items-center">
                    <div class="col">
                        <x-dropdown-input name="spp_symbol" label="ŠPP prvok 1" :values="$spp_symbols"/>
                    </div>
                    <div class="col">
                        <x-checkbox name="reimbursement" label="Refundovať" control="reimbursementShow"></x-checkbox>
                    </div>
                </div>
                <x-hideable-section control="reimbursementShow">
                    <div class="form-row">
                        <div class="col">
                            <x-dropdown-input name="reimbursement_spp" label="ŠPP prvok 2" :values="$spp_symbols"/>
                        </div>
                        <div class="col">
                            <x-simple-input name="reimbursement_date" type="date" label="Dátum vrátenia peňazí"/>
                        </div>
                    </div>
                </x-hideable-section>

            </x-content-section>

            <x-content-section title="Úhrada konferenčného poplatku" x-data="{conferenceFeeShow: false}">
                <x-checkbox name="conference_fee" label="Mám záujem o úhradu konferenčného poplatku pred cestou priamo z pracoviska" control="conferenceFeeShow"/>
                <x-hideable-section control="conferenceFeeShow">
                    <div class="form-row">
                        <div class="col">
                            <x-simple-input name="organiser_name" type="text" label="Názov organizácie"/>
                        </div>
                        <div class="col">
                            <x-simple-input name="ico" type="text" label="IČO"/>
                        </div>
                    </div>
                    <x-simple-input name="organiser_address" type="text" label="Adresa organizácie"/>
                    <div class="form-row">
                        <div class="col">
                            <x-simple-input name="organiser_iban" type="text" label="Číslo účtu organizácie"/>
                        </div>
                        <div class="col">
                            <x-simple-input name="amount" type="text" label="Suma"/>
                        </div>
                    </div>

                </x-hideable-section>
            </x-content-section>

            <div class="container">
            <x-button>Vytvoriť</x-button>
            </div>

        </form>

    </x-content-box>
</x-layout>
