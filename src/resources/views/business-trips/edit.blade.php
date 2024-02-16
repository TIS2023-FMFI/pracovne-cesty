@php
    use App\Models\Country;
    use App\Models\Transport;
    use App\Models\TripPurpose;
    use App\Models\Contribution;
    use App\Models\SppSymbol;
    use App\Enums\TripType;
    use App\Enums\TripState;
    use App\Enums\DocumentType;
    use App\Enums\SppStatus;
    use Illuminate\Support\Facades\Auth;

    $isAdmin = Auth::user()->hasRole('admin');

    $countries = Country::all()->pluck('name', 'id');

    $transportQuery = $isAdmin ?
        Transport::all() :
        Transport::where('user_visible', 1)
            ->orWhere('id', $trip->transport_id);
    $transports = $transportQuery->pluck('name', 'id');

    $purposes = TripPurpose::all()->pluck('name', 'id');

    $contributions = Contribution::all()->pluck('name', 'id');

    $sppSymbolsQuery = SppSymbol::where('status', SppStatus::ACTIVE);
    if ($trip->sppSymbol) {
        $sppSymbolsQuery = $sppSymbolsQuery->orWhere('id', $trip->sppSymbol->id);
    }
    if ($trip->reimbursement) {
        $sppSymbolsQuery = $sppSymbolsQuery->orWhere('id', $trip->reimbursement->sppSymbol->id);
    }

    $spp_symbols = $sppSymbolsQuery
        ->pluck('spp_symbol', 'id')
        ->prepend('žiadny', '');

    $tripType = $trip->type;
    $tripState = $trip->state;
    $tripUserType = $trip->user->user_type;
@endphp

<x-layout>
    <x-content-box title="{{ $trip->tripPurpose->name.' '.$trip->place }}">
        <div class="mb-3">
            <span class="badge badge-pill badge-danger">
            {{ $tripType->inSlovak() }}
            </span>
            <span class="badge badge-pill badge-danger">
            Stav: {{ $trip->state->inSlovak()}}
            </span>
            <span class="badge badge-pill badge-danger">
            Identifikátor: {{ $trip->sofia_id}}
            </span>
        </div>

        <div>
            <div class="alert alert-secondary">
                <x-state-icon :state="$tripState"/>
                {{ $tripState->description() }}
            </div>
        </div>

        <form method="POST" action="{{ route('trip.update', ['trip' => $trip->id]) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <x-content-section
                title="Osobné údaje"
                :disabled="!$isAdmin || $tripState == TripState::CANCELLED">

                <div class="form-row">
                    <div class="col-md col-12">
                        <x-simple-input name="first_name" label="Meno" :value="$trip->user->first_name"/>
                    </div>
                    <div class="col-md col-12">
                        <x-simple-input name="last_name" label="Priezvisko" :value="$trip->user->last_name"/>
                    </div>
                    <div class="col-md col-12">
                        <x-simple-input name="academic_degrees" label="Tituly"
                                        :value="$trip->user->academic_degrees ?? ''"/>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md col-12">
                        <x-simple-input name="address" label="Bydlisko" :value="$trip->user->address ?? ''"/>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md col-12">
                        <x-simple-input name="personal_id" label="Osobné číslo"
                                        :value="$trip->user->personal_id ?? ''"/>
                    </div>
                    <div class="col-md col-12">
                        <x-simple-input name="department" label="Pracovisko" :value="$trip->user->department"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 col-12">
                        <x-simple-input name="iban" label="Číslo účtu" :value="$trip->iban ?? ''"/>
                    </div>
                </div>
            </x-content-section>

            <x-content-section
                :disabled="$tripState == TripState::CANCELLED || (!$isAdmin && $tripState != TripState::CONFIRMED)">
                <div class="form-row">
                    <x-content-section
                        class="col-md col-12"
                        title="Začiatok cesty">

                        <div class="form-row">
                            <div class="col-md col-12">
                                <x-simple-input name="place_start" label="Miesto" :value="$trip->place_start"/>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md col-12">
                                <x-simple-input name="datetime_start" type="datetime-local" label="Dátum a čas"
                                                :value="$trip->datetime_start"/>
                            </div>
                        </div>

                        @if($tripType == TripType::FOREIGN && $tripState != TripState::NEW)
                            <div class="form-row">
                                <div class="col-md col-12">
                                    <x-simple-input name="datetime_border_crossing_start" type="datetime-local"
                                                    label="Dátum a čas prekročenia hraníc"
                                                    :value="$trip->datetime_border_crossing_start ?? ''"/>
                                </div>
                            </div>
                        @endif
                    </x-content-section>

                    <x-content-section
                        class="col-md col-12"
                        title="Koniec cesty">

                        <div class="form-row">
                            <div class="col-md col-12">
                                <x-simple-input name="place_end" label="Miesto" :value="$trip->place_end"/>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md col-12">
                                <x-simple-input name="datetime_end" type="datetime-local" label="Dátum a čas"
                                                :value="$trip->datetime_end"/>
                            </div>
                        </div>

                        @if($tripType == TripType::FOREIGN && $tripState != TripState::NEW)
                            <div class="form-row">
                                <div class="col-md col-12">
                                    <x-simple-input name="datetime_border_crossing_end" type="datetime-local"
                                                    label="Dátum a čas prekročenia hraníc"
                                                    :value="$trip->datetime_border_crossing_end ?? ''"/>
                                </div>
                            </div>
                        @endif
                    </x-content-section>
                </div>

                <div class="form-row">
                    <div class="col-md-6 col-12">
                        <x-dropdown-input name="transport_id" label="Dopravný prostriedok" :values="$transports"
                                          :selected="$trip->transport_id"/>
                    </div>
                </div>
            </x-content-section>

            <x-content-section
                title="Cieľ cesty"
                :disabled="!$isAdmin || $tripState == TripState::CANCELLED">

                <div class="form-row">
                    <div class="col-md col-12">
                        <x-simple-input name="place" label="Miesto" :value="$trip->place"/>
                    </div>
                    <div class="col-md col-12">
                        <x-dropdown-input name="country_id" label="Štát" :values="$countries"
                                          :selected="$trip->country_id"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md col-12">
                        <x-dropdown-input name="trip_purpose_id" label="Účel cesty" :values="$purposes"
                                          :selected="$trip->trip_purpose_id"/>
                    </div>
                    <div class="col-md col-12">
                        <x-textarea name="purpose_details" label="Špecifikácia účelu"
                                    :value="$trip->purpose_details ?? ''"></x-textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md col-12">
                        <x-simple-input name="event_url" label="Link na udalosť" :value="$trip->event_url ?? ''"/>
                    </div>
                </div>

                @php
                    $hasFile = $trip->upload_name != null;
                @endphp
                <div class="form-row">
                    <div class="col-md-6 col-12">
                        <div class="card">
                            <div>
                                <a {{ $hasFile ? 'href=' . route('trip.attachment', ['trip' => $trip->id]) : ''}} class="btn">
                                    <i class="fa fa-download mr-2"></i>
                                    {{ $hasFile ? 'Stiahnuť nahratý súbor' : 'Žiadny súbor nebol nahraný' }}
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </x-content-section>

            @if($tripUserType->isExternal())
                <x-content-section
                    title="Prínos pre fakultu"
                    :disabled="!$isAdmin || $tripState == TripState::CANCELLED">

                    @foreach($contributions as $id => $name)
                        @php
                            $contribution = $trip->contributions->where('id', $id)->first();
                            $detail = addslashes($contribution->pivot->detail ?? '');
                            $checked = $contribution != null;
                        @endphp

                        <div
                            x-data="{ tripContribution: { checked: {{ $checked ? 'true' : 'false' }}, value: '{{ $detail }}' } }"
                            class="input-group mb-3">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <input
                                        type="checkbox"
                                        x-model="tripContribution.checked"
                                        x-on:change="if (!tripContribution.checked) { tripContribution.value = '' }"
                                        name="contribution_{{ $id }}">
                                </div>
                                <span class="input-group-text">{{ $name }}</span>
                            </div>
                            <input type="text" name="contribution_{{ $id }}_detail" x-model="tripContribution.value"
                                   class="form-control">
                        </div>
                    @endforeach

                </x-content-section>
            @endif

            @php
                $isReimbursed = $trip->reimbursement != null;
                $spp2 = $isReimbursed ? $trip->reimbursement->spp_symbol_id : '';
                $reimbursementDate = $isReimbursed ? $trip->reimbursement->reimbursement_date->format('Y-m-d') : '';
            @endphp

            <x-content-section
                title="Financovanie"
                x-data="{reimbursementShow: {{ old('reimbursement', $isReimbursed) ? 'true' : 'false' }} }"
                :disabled="!$isAdmin || $tripState == TripState::CANCELLED">

                <x-slot:description>
                    V prípade refundácie, prosím, vyberte ako <b>ŠPP prvok 2</b> ten prvok, z ktorého budú peniaze
                    neskôr
                    vrátené do <b>ŠPP prvku 1</b>. Ako <b>dátum vrátenia peňazí</b> uveďte iba orientačný, predpokladaný
                    dátum.
                </x-slot:description>
                <div class="form-row align-items-center">
                    <div class="col-md col-12">
                        <x-dropdown-input name="spp_symbol_id" label="ŠPP prvok 1:" :values="$spp_symbols"
                                          :selected="$trip->spp_symbol_id ?? ''"/>
                    </div>
                    <div class="col-md col-12">
                        <x-checkbox name="reimbursement" label="Refundovať" control="reimbursementShow"
                                    :checked="$isReimbursed"></x-checkbox>
                    </div>
                </div>

                <x-hideable-section control="reimbursementShow">
                    <div class="form-row">
                        <div class="col-md col-12">
                            <x-dropdown-input name="reimbursement_spp_symbol_id" label="ŠPP prvok 2"
                                              :values="$spp_symbols"
                                              :selected="$spp2"/>
                        </div>
                        <div class="col-md col-12">
                            <x-simple-input name="reimbursement_date" type="date" label="Dátum vrátenia peňazí"
                                            :value="$reimbursementDate"/>
                        </div>
                    </div>
                </x-hideable-section>
            </x-content-section>

            @php
                $wantsConferenceFee = $trip->conference_fee_id != null;
                $organiser = $wantsConferenceFee ? $trip->conferenceFee->organiser_name : '';
                $ico = $wantsConferenceFee ? $trip->conferenceFee->ico : '';
                $address = $wantsConferenceFee ? $trip->conferenceFee->organiser_address : '';
                $iban = $wantsConferenceFee ? $trip->conferenceFee->iban : '';
                $amount = $wantsConferenceFee ? $trip->conferenceFee->amount : '';
            @endphp

            <x-content-section
                title="Úhrada konferenčného poplatku"
                x-data="{conferenceFeeShow: {{ $wantsConferenceFee ? 'true' : 'false' }} }"
                :disabled="!$isAdmin || $tripState == TripState::CANCELLED">

                <div class="form-row">
                    <div class="col-md col-12">
                        <x-checkbox name="conference_fee"
                                    label="Mám záujem o úhradu konferenčného poplatku pred cestou priamo z pracoviska"
                                    control="conferenceFeeShow" :checked="$wantsConferenceFee"></x-checkbox>
                    </div>
                </div>
                <x-hideable-section control="conferenceFeeShow">
                    <div class="form-row">
                        <div class="col-md col-12">
                            <x-simple-input name="organiser_name" type="text" label="Názov organizácie"
                                            :value="$organiser"/>
                        </div>
                        <div class="col-md col-12">
                            <x-simple-input name="ico" type="text" label="IČO"
                                            :value="$ico ?? ''"/>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md col-12">
                            <x-simple-input name="organiser_address" type="text" label="Adresa organizácie"
                                            :value="$address"/>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md col-12">
                            <x-simple-input name="organiser_iban" type="text" label="Číslo účtu organizácie"
                                            :value="$iban"/>
                        </div>
                        <div class="col-md col-12">
                            <x-simple-input name="amount" type="text" label="Suma"
                                            :value="$amount"/>
                        </div>
                    </div>

                </x-hideable-section>
            </x-content-section>

            @if($tripState->hasTravellerReturned())
                @php
                    $expenses = ['travelling' => 'Cestovné', 'accommodation' => 'Ubytovanie', 'advance' => 'Vložné', 'other' => 'Iné'];
                    if($tripType == TripType::FOREIGN) {
                        $expenses = array_merge($expenses, ['allowance' => 'Záloha za cestu']);
                    }
                    $mealsReimbursement = $trip->meals_reimbursement ?? true;
                    $doesNotWantMeals = old('no_meals_reimbursed', !$mealsReimbursement);
                @endphp

                <x-content-section
                    title="Náklady"
                    x-data="{mealsTableHide: {{ $doesNotWantMeals ? 'true' : 'false'}} }"
                    :disabled="$tripState == TripState::CANCELLED || (!$isAdmin && $tripState != TripState::UPDATED)">

                    <x-slot:description>
                        Pre každý druh nákladov môžete použiť aj oba stĺpce naraz. Ak si preplatenie nejakého druhu
                        nákladov nenárokujete, nezabudnite to, prosím, uviesť.
                    </x-slot:description>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Druh nákladov</th>
                                <th>Suma v EUR</th>
                                @if ($tripType == TripType::FOREIGN)
                                    <th>Suma v cudzej mene</th>
                                @endif
                                <th></th>
                            </tr>
                            </thead>

                            @foreach($expenses as $expenseName => $label)
                                @php
                                    $expense = $trip->{$expenseName . 'Expense'};
                                    $amountEur = $expense->amount_eur ?? '';
                                    $amountForeign = $expense->amount_foreign ?? '';
                                    $reimburse = $expense ? !$expense->reimburse : false;
                                @endphp

                                <tr>
                                    <td>{{ $label }}</td>
                                    <td>
                                        <x-simple-input name="{{ $expenseName }}_expense_eur"
                                                        :value="$amountEur ?? ''"></x-simple-input>
                                    </td>
                                    @if ($tripType == TripType::FOREIGN)
                                        <td>
                                            <x-simple-input name="{{ $expenseName }}_expense_foreign"
                                                            :value="$amountForeign ?? ''"></x-simple-input>
                                        </td>
                                    @endif

                                    <td>
                                        <x-checkbox name="{{ $expenseName }}_expense_not_reimburse"
                                                    :checked="$reimburse" label="Nenárokujem si"></x-checkbox>
                                    </td>
                                </tr>
                            @endforeach

                            <tr>
                                <td>
                                    Stravné
                                </td>
                                <td>
                                    <x-checkbox name="no_meals_reimbursed" label="Nenárokujem si vôbec"
                                                :checked="$doesNotWantMeals" control="mealsTableHide"/>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </table>
                    </div>

                    <div class="form-row">
                        <div class="col">
                            <x-simple-input name="expense_estimation"
                                            label="V prípade pozvania druhou stranou odhadnite preplatené výdavky"
                                            :value="$trip->expense_estimation ?? ''"/>
                        </div>
                    </div>

                    <x-content-section
                        title="Zrážky zo stravného"
                        x-show="!mealsTableHide"
                        class="border-bottom-0 pb-0">

                        <x-slot:description>
                            Vyberte, prosím, ktoré jedlá si <b>nežiadate</b> preplatiť.
                        </x-slot:description>

                        <div class="table-responsive">
                            <table class="table"
                                   x-data="{checkBreakfast: false, checkLunch: false, checkDinner: false}">
                                <thead>
                                <tr>
                                    <th>Dátum</th>
                                    <th>
                                        <x-checkbox name="allBreakfast" control="checkBreakfast" label="Raňajky"/>
                                    </th>
                                    <th>
                                        <x-checkbox name="allLunches" control="checkLunch" label="Obed"/>
                                    </th>
                                    <th>
                                        <x-checkbox name="allDinners" control="checkDinner" label="Večera"/>
                                    </th>
                                </tr>
                                </thead>

                                <tbody>
                                @php
                                    $meals = $trip->not_reimbursed_meals;
                                    $meals = ($meals == null || $meals == '') ? str_repeat('0', $days*3) : $meals;
                                    $currentDate = clone $trip->datetime_start;
                                @endphp
                                @for ($i = 0; $i < $days; $i++)
                                    <tr>
                                        <td>{{ $currentDate->format('d.m.') }}</td>
                                        <td>
                                            <input
                                                type="checkbox"
                                                name="{{ 'b'.$i }}"
                                                x-init="$el.checked = {{$meals[$i * 3 ] === '1' ? 'true' : 'false'}}"
                                                x-bind:checked="checkBreakfast">
                                        </td>
                                        <td>
                                            <input
                                                type="checkbox"
                                                name="{{ 'l'.$i }}"
                                                x-init="$el.checked = {{$meals[$i * 3 + 1] === '1' ? 'true' : 'false'}}"
                                                x-bind:checked="checkLunch">
                                        </td>
                                        <td>
                                            <input
                                                type="checkbox"
                                                name="{{ 'd'.$i }}"
                                                x-init="$el.checked = {{$meals[$i * 3 + 2] === '1' ? 'true' : 'false'}}"
                                                x-bind:checked="checkDinner">
                                        </td>
                                    </tr>

                                    @php $currentDate->modify('+1 day'); @endphp
                                @endfor

                                </tbody>
                            </table>
                        </div>
                    </x-content-section>
                </x-content-section>

                <x-content-section
                    title="Správa"
                    :disabled="$tripState == TripState::CANCELLED || (!$isAdmin && $tripState != TripState::UPDATED)">

                    <x-textarea name="conclusion" label="Výsledky cesty" :value="$trip->conclusion ?? ''"
                                rows="10"></x-textarea>
                </x-content-section>
            @endif

            <div class="d-flex justify-content-end">
                <x-button>{{ $isAdmin ? 'Uložiť úpravy' : 'Potvrdiť údaje' }}</x-button>
            </div>

        </form>
    </x-content-box>

    <x-content-box title="Ďalšie možnosti">
        <x-content-section title="Poznámka k ceste">
            @if($isAdmin)
                <x-slot:description>
                    @if($trip->note)
                        Používateľ zadal poznámku k tejto pracovnej ceste: <b>{{$trip->note}}</b>
                    @endif
                </x-slot:description>
            @else
                <x-slot:description>
                    Tu môžete k pracovnej ceste pridať poznámku pre administrátora, ktorý bude upozornený mailom,
                    poznámka zostane viditeľná aj pre Vás.
                </x-slot:description>
                <form method="POST" action="{{ route('trip.add-comment', ['trip' => $trip->id]) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-row align-items-end">
                        <div class="col-9">
                            <x-textarea name="note" label="Poznámka" :value="$trip->note ?? ''"/>
                        </div>
                        <div class="col-3 my-3 ">
                            <x-button>Pridať poznámku</x-button>
                        </div>
                    </div>
                </form>
            @endif
        </x-content-section>

        @if($isAdmin && $tripState == TripState::NEW)
            <x-content-section title="Potvrdenie cesty">
                <x-slot:description>
                    Po zaevidovaní v systéme SOFIA sem vložte identifikátor cesty a potvrdťe ju. Zmeníte tak jej stav.
                </x-slot:description>
                <form method="POST" action="{{ route('trip.confirm', ['trip' => $trip->id]) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-row align-items-end">
                        <div class="col-9">
                            <x-simple-input name="sofia_id" label="Identifikátor"></x-simple-input>
                        </div>
                        <div class="col-3 my-3 ">
                            <x-button>Potvrdiť cestu</x-button>
                        </div>
                    </div>
                </form>
            </x-content-section>
        @endif

        @if($isAdmin && $tripState == TripState::COMPLETED)
            <x-content-section title="Vyúčtovanie cesty">
                <x-slot:description>
                    Tu si môžete označiť, že ste zaevidovali správu a náklady v systéme SOFIA. Zmeníte tak stav cesty na
                    uzavretú.
                </x-slot:description>
                <form method="POST" action="{{ route('trip.close', ['trip' => $trip->id]) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-row align-items-end">
                        <div class="col my-3 ">
                            <x-button>Vyúčtovanie vytvorené</x-button>
                        </div>
                    </div>
                </form>
            </x-content-section>
        @endif

        @if(!$isAdmin && in_array($tripState, [TripState::NEW, TripState::CONFIRMED]))
            <x-content-section title="Žiadosť o storno">
                <x-slot:description>
                    Môžete požiadať o storno pracovnej cesty, musíte však uviesť dôvod storna. Cesta bude stornovaná až
                    po schválení administrátorom.
                </x-slot:description>
                <form method="POST" action="{{ route('trip.request-cancel', ['trip' => $trip->id]) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-row align-items-end">
                        <div class="col-9">
                            <x-textarea name="cancellation_reason" label="Dôvod storna"></x-textarea>
                        </div>
                        <div class="col-3 my-3">
                            <x-button color="danger">Odoslať žiadosť</x-button>
                        </div>
                    </div>
                </form>
            </x-content-section>
        @endif

        @if($isAdmin && $tripState != TripState::CANCELLED && !$tripState->hasTravellerReturned())
            <x-content-section title="Stornovanie">
                <x-slot:description>
                    <p>Ako administrátor môžete stornovať pracovnú cestu.</p>
                    @if($tripState == TripState::CANCELLATION_REQUEST)
                        <p>Zadaný dôvod storna od používateľa: <b>{{ $trip->cancellation_reason ?? 'prázdny' }}</b></p>
                    @endif
                </x-slot:description>
                <form method="POST" action="{{ route('trip.cancel', ['trip' => $trip->id]) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-row align-items-end">
                        <div class="col">
                            <x-button color="danger">Stornovať</x-button>
                        </div>
                    </div>
                </form>
            </x-content-section>
        @endif

        @if($tripState != TripState::NEW)
            <x-content-section title="Dokumenty na stiahnutie">
                <x-slot:description>
                    Tu sa nachádzajú všetky relevantné dokumenty k ceste podľa jej stavu a typu používateľa. Ak v ceste
                    urobíte nejaké zmeny, nezabudnite ich uložiť, aby ste v dokumentoch vždy mali aktuálne údaje.
                </x-slot:description>

                <div>
                    @if($tripUserType->isExternal())
                        <x-document-export-icon :id="$trip->id" :docType="DocumentType::COMPENSATION_AGREEMENT"/>
                    @endif

                    @if($wantsConferenceFee)
                        <x-document-export-icon :id="$trip->id" :docType="DocumentType::PAYMENT_ORDER"/>
                        <x-document-export-icon :id="$trip->id" :docType="DocumentType::CONTROL_SHEET"/>
                    @endif

                    @if($tripType == TripType::FOREIGN)
                        <x-document-export-icon :id="$trip->id" :docType="DocumentType::FOREIGN_TRIP_AFFIDAVIT"/>
                    @endif

                    @if(in_array($tripState, [TripState::COMPLETED, TripState::CLOSED]))
                        @if($tripType == TripType::FOREIGN)
                            <x-document-export-icon :id="$trip->id" :docType="DocumentType::FOREIGN_REPORT"/>
                        @else
                            <x-document-export-icon :id="$trip->id" :docType="DocumentType::DOMESTIC_REPORT"/>
                        @endif
                    @endif

                </div>
            </x-content-section>
        @endif

    </x-content-box>
</x-layout>
