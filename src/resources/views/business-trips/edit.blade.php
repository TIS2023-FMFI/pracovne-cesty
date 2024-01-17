@php
    use App\Models\Country;
    use App\Models\Transport;
    use App\Models\TripPurpose;
    use App\Models\Contribution;
    use App\Models\SppSymbol;
    use App\Enums\TripType;

    $countries = Country::all()->pluck('name', 'id')->toArray();
    $transports = Transport::all()->pluck('name', 'id')->toArray();
    $purposes = TripPurpose::all()->pluck('name', 'id')->toArray();;
    $contributions = Contribution::all()->pluck('name', 'id')->toArray();
    $spp_symbols = SppSymbol::all()->pluck('spp_symbol', 'id')->toArray();

    $tripType = $trip->type;
@endphp

<x-layout>
    <x-content-box title="{{ $trip->tripPurpose->name.' '.$trip->place }}">
        <div class="mb-3">
            <span class="badge badge-pill badge-danger">
            {{ $tripType == TripType::DOMESTIC ? "Tuzemská cesta" : "Zahraničná cesta"}}
            </span>
            <span class="badge badge-pill badge-danger">
            Stav: {{ $trip->state}}
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
                    @if($tripType == TripType::FOREIGN)
                        <div class="col">
                            <x-dropdown-input name="country" label="Štát" :values="$countries" :selected="$trip->country_id"/>
                        </div>
                    @endif
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
                        <x-simple-input name="upload_name" type="file"
                                        label="Vložte pozvánku, plagát alebo iný súbor..."/>
                    </div>
                    <div class="col">
                        <x-simple-input name="event_url" label="Link na udalosť" :value="$trip->event_url ?? ''"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-6">
                        <x-dropdown-input name="transport" label="Dopravný prostriedok" :values="$transports"
                                          :selected="$trip->transport_id"/>
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

            @php
                $isReimbursed = $trip->reimbursement != null;
                $spp2 = $isReimbursed ? $trip->reimbursement->spp_symbol_id : '';
                $reimbursementDate = $isReimbursed ? $trip->reimbursement->reimbursement_date : '';
            @endphp

            <x-content-section title="Financovanie" x-data="{reimbursementShow: {{ $isReimbursed }} }">
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
                                            :value="$reimbursementDate->format('Y-m-d')"/>
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
                               x-data="{conferenceFeeShow: {{ $wantsConferenceFee }} }">
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

                    <div class="container">
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

                            @for ($i = 0; $i < 5; $i++)
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

            </x-content-section>


            <x-content-section title="Správa">
                <x-textarea name="conclusion" label="Výsledky cesty" :value="$trip->conclusion ?? ''"></x-textarea>
            </x-content-section>

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
            <form method="POST" action="/trips/{{ $trip->id }}/">
                @csrf
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

        <x-content-section title="Žiadosť o storno">
            <x-slot:description>
                Môžete požiadať o storno pracovnej cesty, musíte však uviesť dôvod storna. Cesta bude stornovaná až po schválení administrátorom.
            </x-slot:description>
            <form method="POST" action="/trips/{{ $trip->id }}/">
                @csrf
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

        <x-content-section title="Dokumenty na stiahnutie">
            <div>
                <div>
                    <a href="/export/{{ $trip->id }}?fileType=" class="text-decoration-none text-dark">
                        <i class="fa-solid fa-file-pdf fa-2xl"></i>
                        <div>Správa zo zahraničnej pracovnej cesty</div>
                    </a>
                </div>

            </div>
        </x-content-section>

    </x-content-box>
</x-layout>
