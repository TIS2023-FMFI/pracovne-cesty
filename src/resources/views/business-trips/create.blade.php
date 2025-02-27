@php
    use App\Models\Country;
    use App\Models\Transport;
    use App\Models\TripPurpose;
    use App\Models\Contribution;
    use App\Models\SppSymbol;
    use App\Enums\SppStatus;

    $old_country_id = old("country_id");
    $old_transport_id = old("transport_id");
    $old_trip_purpose_id = old("trip_purpose_id");
    $old_spp_symbol_id = old("spp_symbol_id");
    $old_reimbursement_spp_symbol_id = old("reimbursement_spp_symbol_id");

    $sortedCountries = Country::getSortedByTripsCount();
    $countries = Country::makeSlovakiaFirst($sortedCountries)->pluck('name', 'id');
    $transports = Transport::where('user_visible', 1)->pluck('name', 'id');
    $purposes = TripPurpose::all()->pluck('name', 'id');
    $contributions = Contribution::all()->pluck('name', 'id');

    $sppSymbolsQuery = SppSymbol::where('status', SppStatus::ACTIVE)->orderBy('spp_symbol', 'ASC');

    $spp_symbols = $sppSymbolsQuery
    ->get()
    ->mapWithKeys(function ($spp) {
        $details = array_filter([$spp->agency, $spp->acronym]);

        $grantee = $spp->granteeUser
        ? $spp->granteeUser->first_name . ' ' . $spp->granteeUser->last_name
        : 'Unknown';

        if ($spp->granteeUser?->academic_degrees) {
        $grantee .= ' (' . $spp->granteeUser->academic_degrees . ')';
        }

        return [$spp->id => $spp->spp_symbol . ' - ' . (count($details) ? implode(', ', $details) . ', ' : '') . $grantee];
    });

    $user = $selectedUser ?? Auth::user();
    $userType = $user->user_type;

@endphp

<x-layout>
    <x-content-box title="Nová pracovná cesta">
        @error('duplicate')
        <p><span style="color: red;">{{ $message }}</span></p>
        @enderror
        <form method="POST" action="{{ route('trip.store') }}" enctype="multipart/form-data">
            @csrf
            <x-simple-input name="target_user" :value="$user->id" hidden/>
            <x-content-section title="Osobné údaje">
                <div class="form-row">
                    <div class="col-md col-12">
                        <x-simple-input name="first_name" label="Meno" :value="$user->first_name"/>
                    </div>
                    <div class="col-md col-12">
                        <x-simple-input name="last_name" label="Priezvisko" :value="$user->last_name"/>
                    </div>
                    <div class="col-md col-12">
                        <x-simple-input name="academic_degrees" label="Tituly" :value="$user->academic_degrees ?? ''"/>
                    </div>
                </div>

                <x-simple-input name="address" label="Bydlisko" :value="$user->address ?? ''"/>

                <div class="form-row">
                    <div class="col-md col-12">
                        <x-simple-input name="personal_id" label="Osobné číslo" :readonly="true" :value="$user->personal_id ?? ''"/>
                    </div>
                    <div class="col-md col-12">
                        <x-simple-input name="department" label="Pracovisko" :value="$user->department ?? ''"/>
                    </div>
                </div>

                <div class="form-row">
                    <x-tooltip
                        text="Číslo účtu je podľa možností už predvyplnené na základe údajov z profilu cestujúceho /
                        predchádzajúcej pracovnej cesty. Ak si želáte použiť dané čislo účtu, nemusíte robiť nič.
                        Pre použitie iného čísla účtu len pre túto pracovnú cestu stačí, keď dané číslo zadáte do
                        príslušného poľa. Ak si však želáte, aby sa toto nové číslo účtu zobrazovalo ako prednastavené
                        pre budúce pracovné cesty, zaškrtnite prosím aj príslušné okienko na jeho uloženie."
                        icon="question-circle">
                    </x-tooltip>
                    <div class="col-md-6 col-12">
                        <x-simple-input name="iban" label="Číslo účtu" :value="$user->iban ?? ''"/>
                    </div>
                    <div class="mr-2 ml-2 mt-5">
                        <input type = 'checkbox' name='storeIban'> Uložiť číslo účtu?
                    </div>
                </div>
            </x-content-section>

            <x-content-section>
                <div class="form-row">
                    <x-content-section
                        title="Začiatok cesty"
                        class="col-md col-12">
                        <x-simple-input name="place_start" label="Miesto"/>
                        <x-simple-input name="datetime_start" type="datetime-local" label="Dátum a čas"/>
                    </x-content-section>

                    <x-tooltip
                        text='Pod pojmom "Koniec cesty" sa rozumie konkrétne miesto, kde sa vaša cesta skutočne ukončí
                        (napríklad návrat domov), a nie miesto, kam primárne cestujete za účelom pracovnej cesty.
                        Príklad: "vyrážam z Bratislavy na konferenciu do Viedne, odkiaľ sa potom vrátim priamo domov
                        do Trnavy". Koniec cesty je v tomto prípade Trnava, zataľ čo Viedeň je "Cieľ cesty", ktorý sa
                        vypĺňa nižšie.'
                        icon="question-circle">
                    </x-tooltip>

                    <x-content-section
                        title="Koniec cesty"
                        class="col-md col-12">
                        <x-simple-input name="place_end" label="Miesto"/>
                        <x-simple-input name="datetime_end" type="datetime-local" label="Dátum a čas"/>
                    </x-content-section>
                </div>

                <div class="form-row">
                    <div class="col-md-6 col-12">
			<x-dropdown-input name="transport_id" label="Dopravný prostriedok" :values="$transports"
                             :selected="$old_transport_id" />
                    </div>
                </div>

            </x-content-section>

            <x-content-section title="Cieľ cesty">
                <div class="form-row">
                    <div class="col-md col-12">
                        <x-simple-input name="place" label="Miesto"/>
                    </div>
		    <div class="col-md col-12">
                        <x-dropdown-input name="country_id" label="Štát" :values="$countries"
			          :selected="$old_country_id" />
                    </div>

                </div>
                <div class="form-row">
                    <div class="col-md col-12">
			<x-dropdown-input name="trip_purpose_id" label="Účel cesty" :values="$purposes"
                             :selected="$old_trip_purpose_id" />
                    </div>
                    <div class="col-md col-12">
                        <x-textarea name="purpose_details" label="Špecifikácia účelu"></x-textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <x-simple-input name="event_url" label="Link na udalosť"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 col-12">
                        <x-simple-input name="upload_name" type="file" label="Vložte pozvánku, plagát alebo iný súbor..."/>
                    </div>
                </div>
            </x-content-section>

            @if($userType->isExternal())
                <x-content-section title="Prínos pre fakultu">
                    <x-slot:description>
                        Označte prínosy pre fakultu, ktoré sa týkajú Vašej pracovnej cesty. Môžete ich aj špecifikovať.
                    </x-slot:description>

                    @foreach($contributions as $id => $name)
                        <div x-data="{ contributionDetail: { checked: false, value: '' } }" class="input-group mb-3">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <input type="checkbox" name="contribution_{{ $id }}" x-model="contributionDetail.checked" x-on:change="if (!contributionDetail.checked) { contributionDetail.value = '' }">
                                </div>
                                <span class="input-group-text">{{ $name }}</span>
                            </div>
                            <input type="text" name="contribution_{{ $id }}_detail" x-model="contributionDetail.value" class="form-control">
                        </div>
                    @endforeach
                </x-content-section>
            @endif

            @php
                $reimbursementShow = old('reimbursement');
            @endphp
            <x-content-section title="Financovanie" x-data="{reimbursementShow: {{$reimbursementShow ? 'true' : 'false'}} }">
                <x-slot:description>
                    V prípade refundácie, prosím, vyberte ako <b>ŠPP prvok 2</b> ten prvok, z ktorého budú peniaze neskôr
                    vrátené do <b>ŠPP prvku 1</b>. Ako <b>dátum vrátenia peňazí</b> uveďte iba orientačný, predpokladaný dátum.
                </x-slot:description>
                <div class="form-row align-items-center">
                    <div class="col-md col-12">
                        <x-dropdown-input name="spp_symbol_id" label="ŠPP prvok 1 (vyberte prázdny, ak sa doplní ručne):" :values="$spp_symbols"
                           :selected="$old_spp_symbol_id" />

                    </div>
                    <div class="col-md col-12">
                        <x-checkbox name="reimbursement" label="Refundovať" control="reimbursementShow"></x-checkbox>
                    </div>
                </div>
                <x-hideable-section control="reimbursementShow">
                    <div class="form-row">
                        <div class="col-md col-12">
                            <x-dropdown-input name="reimbursement_spp_symbol_id" label="ŠPP prvok 2"
					      :values="$spp_symbols"
                                              :selected="$old_reimbursement_spp_symbol_id"/>
                        </div>
                        <div class="col-md col-12">
                            <x-simple-input name="reimbursement_date" type="date" label="Dátum vrátenia peňazí"/>
                        </div>
                    </div>
                </x-hideable-section>

            </x-content-section>

            @php
                $conferenceFeeShow = old('conference_fee');
            @endphp
            <x-content-section title="Úhrada konferenčného poplatku" x-data="{conferenceFeeShow: {{$conferenceFeeShow ? 'true' : 'false'}} }">
                <x-checkbox name="conference_fee" label="Mám záujem o úhradu konferenčného poplatku pred cestou priamo z pracoviska" control="conferenceFeeShow"/>
                <x-hideable-section control="conferenceFeeShow">
                    <div class="form-row">
                        <div class="col-md col-12">
                            <x-simple-input name="organiser_name" type="text" label="Názov organizácie"/>
                        </div>
                        <div class="col-md col-12">
                            <x-simple-input name="ico" type="text" label="IČO"/>
                        </div>
                    </div>
                    <x-simple-input name="organiser_address" type="text" label="Adresa organizácie"/>
                    <div class="form-row">
                        <div class="col-md col-12">
                            <x-simple-input name="organiser_iban" type="text" label="Číslo účtu organizácie"/>
                        </div>
                        <div class="col-md col-12">
                            <x-simple-input name="amount" type="text" label="Suma"/>
                        </div>
                    </div>
                </x-hideable-section>
            </x-content-section>

            <div class="d-flex justify-content-end">
                <x-button>Pridať cestu</x-button>
            </div>

        </form>

    </x-content-box>
</x-layout>
