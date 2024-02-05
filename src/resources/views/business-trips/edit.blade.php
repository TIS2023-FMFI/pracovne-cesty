@php
    use App\Models\Country;
    use App\Models\Transport;
    use App\Models\TripPurpose;
    use App\Models\Contribution;
    use App\Models\SppSymbol;
    use App\Enums\TripType;
    use App\Enums\TripState;
    use App\Enums\DocumentType;
    use App\Enums\UserType;
    use App\Enums\SppStatus;

    $countries = Country::all()->pluck('name', 'id');
    $transports = Transport::all()->pluck('name', 'id');
    $purposes = TripPurpose::all()->pluck('name', 'id');
    $contributions = Contribution::all()->pluck('name', 'id');
    $spp_symbols = SppSymbol::where('status', SppStatus::ACTIVE)->pluck('spp_symbol', 'id');

    $tripType = $trip->type;
    $tripState = $trip->state;
    $tripUserType = $trip->user->user_type;
@endphp

<x-layout>
    <x-content-box title="{{ $trip->tripPurpose->name.' '.$trip->place }}">
        <div class="mb-3">
            <span class="badge badge-pill badge-danger">
            {{ $tripType == TripType::DOMESTIC ? "Tuzemská cesta" : "Zahraničná cesta"}}
            </span>
            <span class="badge badge-pill badge-danger">
            Stav: {{ $trip->state->inSlovak()}}
            </span>
            <span class="badge badge-pill badge-danger">
            Identifikátor: {{ $trip->sofia_id ?? '0000'}}
            </span>
        </div>

        <form method="POST" action="/trips/{{ $trip->id }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <x-content-section title="Osobné údaje">
                <div class="form-row">
                    <div class="col">
                        <x-simple-input name="first_name" label="Meno" :value="$trip->user->first_name"/>
                    </div>
                    <div class="col">
                        <x-simple-input name="last_name" label="Priezvisko" :value="$trip->user->last_name"/>
                    </div>
                    <div class="col">
                        <x-simple-input name="academic_degrees" label="Tituly" :value="$trip->user->academic_degrees"/>
                    </div>
                </div>

                <x-simple-input name="address" label="Bydlisko" :value="$trip->user->address"/>

                <div class="form-row">
                    <div class="col">
                        <x-simple-input name="personal_id" label="Osobné číslo" :value="$trip->user->personal_id"/>
                    </div>
                    <div class="col">
                        <x-simple-input name="department" label="Pracovisko" :value="$trip->user->department"/>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-6">
                        <x-simple-input name="iban" label="Číslo účtu" :value="$trip->user->iban ?? ''"/>
                    </div>

                </div>

            </x-content-section>
            <div class="container">
                <div class="row">
                    <div class="col">
                        <x-content-section title="Začiatok cesty">
                            <x-simple-input name="place start" label="Miesto" :value="$trip->place_start"/>
                            <x-simple-input name="datetime_start" type="datetime-local" label="Dátum a čas"
                                            :value="$trip->datetime_start"/>
                            @if($tripType == TripType::FOREIGN)
                                <x-simple-input name="datetime_border_crossing_start" type="datetime-local"
                                                label="Dátum a čas prekročenia hraníc"
                                                :value="$trip->datetime_border_crossing_start ?? ''"/>
                            @endif
                        </x-content-section>
                    </div>
                    <div class="col">
                        <x-content-section title="Koniec cesty">
                            <x-simple-input name="place_end" label="Miesto" :value="$trip->place_end"/>
                            <x-simple-input name="datetime_end" type="datetime-local" label="Dátum a čas"
                                            :value="$trip->datetime_end"/>
                            @if($tripType == TripType::FOREIGN)
                                <x-simple-input name="datetime_border_crossing_end" type="datetime-local"
                                                label="Dátum a čas prekročenia hraníc"
                                                :value="$trip->datetime_border_crossing_end ?? ''"/>
                            @endif
                        </x-content-section>
                    </div>
                </div>
            </div>

            <x-content-section title="Cieľ cesty">
                <div class="form-row">
                    <div class="col">
                        <x-simple-input name="place" label="Miesto" :value="$trip->place"/>
                    </div>
                    <div class="col">
                        <x-dropdown-input name="country" label="Štát" :values="$countries" :selected="$trip->country_id"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col">
                        <x-dropdown-input name="trip_purpose" label="Účel cesty" :values="$purposes"
                                          :selected="$trip->trip_purpose_id"/>
                    </div>
                    <div class="col">
                        <x-textarea name="purpose_details" label="Špecifikácia účelu"
                                    :value="$trip->purpose_details ?? ''"></x-textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col">
                        <x-dropdown-input name="transport" label="Dopravný prostriedok" :values="$transports"
                                          :selected="$trip->transport_id"/>
                    </div>
                    <div class="col">
                        <x-simple-input name="event_url" label="Link na udalosť" :value="$trip->event_url ?? ''"/>
                    </div>
                </div>

                @php
                    $hasFile = $trip->upload_name != null;
                @endphp
                <div class="form-row">
                    <div class="col-sm-6">

                        <div class="card">
                            <div>
                                <a {{ $hasFile ? 'href=/trips/' . $trip->id . '/attachment' : ''}} class="btn">
                                    <i class="fa fa-download mr-2"></i>
                                    {{ $hasFile ? 'Stiahnuť nahratý súbor' : 'Žiadny súbor nebol nahraný' }}
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </x-content-section>

            @if(in_array($tripUserType, [UserType::STUDENT, UserType::EXTERN]))
                <x-content-section title="Prínos pre fakultu">
                    @foreach($contributions as $id => $name)
                        @php
                            $contribution = $trip->contributions->where('id', $id)->first();
                            $detail = addslashes($contribution->pivot->detail ?? '');
                            $checked = $contribution != null;
                        @endphp

                        <div x-data="{ tripContribution: { checked: {{ $checked ? 'true' : 'false' }}, value: '{{ $detail }}' } }" class="input-group mb-3">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <input type="checkbox" x-model="tripContribution.checked" x-on:change="if (!tripContribution.checked) { tripContribution.value = '' }">
                                </div>
                                <span class="input-group-text">{{ $name }}</span>
                            </div>
                            <input type="text" name="contribution_{{ $id }}_detail" x-model="tripContribution.value" class="form-control">
                        </div>
                    @endforeach

                </x-content-section>
            @endif

            @php
                $isReimbursed = $trip->reimbursement != null;
                $spp2 = $isReimbursed ? $trip->reimbursement->spp_symbol_id : '';
                $reimbursementDate = $isReimbursed ? $trip->reimbursement->reimbursement_date->format('Y-m-d') : '';
            @endphp

            <x-content-section title="Financovanie" x-data="{reimbursementShow: {{ $isReimbursed ? 'true' : 'false' }} }">
                <x-slot:description>
                    V prípade refundácie, prosím, vyberte ako ŠPP prvok 2 ten prvok, z ktorého budú peniaze neskôr
                    vrátené do ŠPP prvku 1. Ako dátum vrátenia peňazí uveďte iba orientačný, predpokladaný dátum.
                </x-slot:description>
                <div class="form-row align-items-center">
                    <div class="col">
                        <x-dropdown-input name="spp_symbol" label="ŠPP prvok 1:" :values="$spp_symbols"
                                          :selected="$trip->spp_symbol_id"/>
                    </div>
                    <div class="col">
                        <x-checkbox name="reimbursement" label="Refundovať" control="reimbursementShow"
                                    :checked="$isReimbursed"></x-checkbox>
                    </div>
                </div>

                <x-hideable-section control="reimbursementShow">
                    <div class="form-row">
                        <div class="col">
                            <x-dropdown-input name="reimbursement_spp" label="ŠPP prvok 2" :values="$spp_symbols"
                                              :selected="$spp2"/>
                        </div>
                        <div class="col">
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

            <x-content-section title="Úhrada konferenčného poplatku"
                               x-data="{conferenceFeeShow: {{ $wantsConferenceFee ? 'true' : 'false' }} }">
                <x-checkbox name="conference_fee"
                            label="Mám záujem o úhradu konferenčného poplatku pred cestou priamo z pracoviska"
                            control="conferenceFeeShow" :checked="$wantsConferenceFee"></x-checkbox>
                <x-hideable-section control="conferenceFeeShow">
                    <div class="form-row">
                        <div class="col">
                            <x-simple-input name="organiser_name" type="text" label="Názov organizácie"
                                            :value="$organiser"/>
                        </div>
                        <div class="col">
                            <x-simple-input name="ico" type="text" label="IČO"
                                            :value="$ico ?? ''"/>
                        </div>
                    </div>
                    <x-simple-input name="organiser_address" type="text" label="Adresa organizácie"
                                    :value="$address"/>
                    <div class="form-row">
                        <div class="col">
                            <x-simple-input name="organiser_iban" type="text" label="Číslo účtu organizácie"
                                            :value="$iban"/>
                        </div>
                        <div class="col">
                            <x-simple-input name="amount" type="text" label="Suma"
                                            :value="$amount"/>
                        </div>
                    </div>

                </x-hideable-section>
            </x-content-section>

            @if(in_array($tripState, [TripState::UPDATED, TripState::COMPLETED, TripState::CLOSED]) )

                @php
                    $expenses = ['travelling' => 'Cestovné', 'accommodation' => 'Ubytovanie', 'allowance' => 'Záloha za cestu', 'advance' => 'Vložné', 'other' => 'Iné'];
                    $mealsReimbursement = $trip->meals_reimbursement ?? true;
                    $doesNotWantMeals = !$mealsReimbursement;
                @endphp
                <x-content-section title="Náklady" x-data="{mealsTableHide: {{ $doesNotWantMeals ? 'true' : 'false'}} }">
                    <x-slot:description>
                        Pre každý druh nákladov môžete použiť aj oba stĺpce naraz. Ak si preplatenie nejakého druhu nákladov nenárokujete, nezabudnite to, prosím, uviesť.
                    </x-slot:description>

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
                                $reimburse = $expense->reimburse ?? false;
                            @endphp
                            <tr>
                                <td>
                                    {{ $label }}
                                </td>
                                <td>
                                    <x-simple-input name="{{ $expenseName }}_expense_eur"
                                                    :value="$amountEur ?? ''"></x-simple-input>
                                </td>
                                @if ($tripType == TripType::FOREIGN)
                                    <td>
                                        <x-simple-input name="{{ $expenseName }}_expense_foreign" :value="$amountForeign ?? ''"></x-simple-input>
                                    </td>
                                @endif

                                <td>
                                    <x-checkbox name="{{ $expenseName }}_expense_reimburse" :checked="$reimburse" label="Nenárokujem si"></x-checkbox>
                                </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td>
                                Stravné
                            </td>
                            <td>
                                <x-checkbox name="no_meals_reimbursed" label="Nenárokujem si vôbec" :checked="$doesNotWantMeals" control="mealsTableHide"/>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </table>

                    <x-content-section title="Zrážky zo stravného" x-show="!mealsTableHide">
                        <x-slot:description>
                            Vyberte, prosím, ktoré jedlá si <b>nežiadate</b> preplatiť.
                        </x-slot:description>

                        <div>
                            <table class="table" x-data="{checkBreakfast: false, checkLunch: false, checkDinner: false}">
                                <thead>
                                <tr>
                                    <th>Dátum</th>
                                    <th><x-checkbox name="allBreakfast" control="checkBreakfast" label="Raňajky"/></th>
                                    <th><x-checkbox name="allLunches" control="checkLunch" label="Obed"/></th>
                                    <th><x-checkbox name="allDinners" control="checkDinner" label="Večera"/></th>
                                </tr>
                                </thead>

                                <tbody>
                                @php
//                                 $meals = $trip->not_reimbursed_meals;
                                    $meals = $meals ?? str_repeat('0', $days*3);
                                    $currentDate = clone $trip->datetime_start;
                                @endphp
                                @for ($i = 0; $i < $days; $i++)
                                    <tr>
                                        <td>{{ $currentDate->format('d.m.') }}</td>
                                        <td>
                                            <input type="checkbox" :name="{{ 'b'.$i }}" x-init="$el.checked = '{{$meals[$i * 3]}}' === '1'" x-bind:checked="checkBreakfast">
                                        </td>
                                        <td>
                                            <input type="checkbox" :name="{{ 'l'.$i }}" x-init="$el.checked = '{{$meals[$i * 3 + 1]}}' === '1'" x-bind:checked="checkLunch">
                                        </td>
                                        <td>
                                            <input type="checkbox" :name="{{ 'd'.$i }}" x-init="$el.checked = '{{$meals[$i * 3 + 2]}}' === '1'" x-bind:checked="checkDinner" >
                                        </td>
                                    </tr>

                                    @php $currentDate->modify('+1 day'); @endphp
                                @endfor

                                </tbody>
                            </table>
                        </div>
                    </x-content-section>

                </x-content-section>


                <x-content-section title="Správa">
                    <x-textarea name="conclusion" label="Výsledky cesty" :value="$trip->conclusion ?? ''" rows="10"></x-textarea>
                </x-content-section>

            @endif

            <div class="d-flex justify-content-end">
                <x-button>Uložiť úpravy</x-button>
            </div>

        </form>
    </x-content-box>

    <x-content-box title="Ďalšie možnosti">
        <x-content-section title="Poznámka pre administrátora">
            <x-slot:description>
                Tu môžete k pracovnej ceste pridať poznámku pre administrátora, ktorý bude upozornený mailom, poznámka zostane viditeľná aj pre Vás.
            </x-slot:description>
            <form method="POST" action="/trips/{{ $trip->id }}/note">
                @csrf
                @method('PUT')
                <div class="form-row align-items-end">
                    <div class="col-9">
                        <x-textarea name="note" label="Poznámka"></x-textarea>
                    </div>
                    <div class="col-3 my-3 ">
                        <x-button>Pridať poznámku</x-button>
                    </div>
                </div>
            </form>
        </x-content-section>

        @if($tripState == TripState::NEW)
            <x-content-section title="Potvrdenie cesty">
                <x-slot:description>
                    Po zaevidovaní v systéme SOFIA sem vložte identifikátor cesty a potvrdťe ju. Zmeníte tak jej stav.
                </x-slot:description>
                <form method="POST" action="/trips/{{ $trip->id }}/confirm">
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

        @if($tripState == TripState::COMPLETED)
            <x-content-section title="Vyúčtovanie cesty">
                <x-slot:description>
                    Tu si môžete označiť, že ste zaevidovali správu a náklady v systéme SOFIA. Zmeníte tak stav cesty na uzavretú.
                </x-slot:description>
                <form method="POST" action="/trips/{{ $trip->id }}/close">
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

        @if(in_array($tripState, [TripState::NEW, TripState::CONFIRMED]))
            <x-content-section title="Žiadosť o storno">
                <x-slot:description>
                    Môžete požiadať o storno pracovnej cesty, musíte však uviesť dôvod storna. Cesta bude stornovaná až po schválení administrátorom.
                </x-slot:description>
                <form method="POST" action="/trips/{{ $trip->id }}/request_cancellation">
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

        @if(in_array($tripState, [TripState::NEW, TripState::CONFIRMED, TripState::CANCELLATION_REQUEST]))
            <x-content-section title="Stornovanie">
                <x-slot:description>
                    Ako administrátor môžete stornovať pracovnú cestu.
                </x-slot:description>
                <form method="POST" action="/trips/{{ $trip->id }}/cancel">
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
                <div>
                    @if(in_array($tripUserType, [UserType::EXTERN, UserType::STUDENT]))
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
