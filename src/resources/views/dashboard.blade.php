@php
    use App\Models\User;
    use App\Enums\UserStatus;

    $isAdmin = Auth::user()->hasRole('admin');

    $selectedUserId = request()->query('user');
    $selectedUser = $selectedUserId ? User::find($selectedUserId) : null;
    $selectedUserName = $selectedUser ? $selectedUser->first_name.' '.$selectedUser->last_name : '';
    $visibleUsersStatus = request('inactive') == 'true' ? UserStatus:: INACTIVE : UserStatus::ACTIVE;
@endphp

<x-layout>
    <div class="mb-3 btn-toolbar" role="toolbar">
        <div class="mr-2 btn-group">
            <x-link-button
              href="{{ $selectedUserId ? route('trip.create', ['user' => $selectedUserId]) : route('trip.create') }}">Pridať pracovnú cestu
                @if($isAdmin && $selectedUserName)
                    <x-slot:detail>
                        ako {{ $selectedUserName }}
                    </x-slot:detail>
                @endif
            </x-link-button>
        </div>

        @if($isAdmin)
            <div class="mr-2 btn-group">
                <x-button color="danger" modal="add-users">Pridať používateľov</x-button>
            </div>

        <div class="mr-2 btn-group">
            <x-link-button color="danger" href="{{ route('spp.manage') }}">ŠPP prvky</x-link-button>
        </div>

            <div class="mr-2 btn-group">
                @if(request('inactive')=='true')
                <x-link-button color="danger"
                href="{!!route('homepage', ['filter'=>request('filter'),'sort'=>request('sort'),'inactive'=>'false'])!!}"
                >Aktívni používatelia</x-link-button>
                @else
                <x-link-button color="danger"
                href="{!!route('homepage', ['filter'=>request('filter'),'sort'=>request('sort'),'inactive'=>'true'])!!}"
                >Neaktívni používatelia</x-link-button>
                @endif
            </div>

        @endif

        <div class="mr-2 btn-group">
            <x-link-button color="danger"
                href="{{route('instruction') }}">Návod
            </x-link-button>
        </div>
    </div>

    @if($isAdmin)
        <x-modals.add-user/>
        <x-modals.activate-user/>
        <x-modals.deactivate-user/>
    @endif

    <div class="row">
        @if($isAdmin)
            <div class="col-md-4">
                <x-content-box title="Prehľad {{request('inactive') == 'true' ? '(Neaktívni)' : '(Aktívni)'}}">
                    <x-overview-item :ref="route('homepage', ['filter' => 'all', 'sort' => request('sort'), 'inactive' => request('inactive')])"><b>Všetky</b></x-overview-item>
                    <x-overview-item :ref="route('homepage', ['filter' => 'unconfirmed', 'sort' => request('sort'), 'inactive' => request('inactive')])"><b>Nepotvrdené</b></x-overview-item>
                    <x-overview-item :ref="route('homepage', ['filter' => 'unaccounted', 'sort' => request('sort'), 'inactive' => request('inactive')])"><b>Nevyúčtované</b></x-overview-item>

                    <div class="my-3"></div>

                    @foreach($users as $user)
                        @if($user['status'] == $visibleUsersStatus)
                        <x-overview-item :ref="route('homepage', ['user' => $user->id, 'sort' => request('sort'), 'inactive' => request('inactive')])">
                            {{$user->last_name.' '.$user->first_name.' ' }}</x-overview-item>
                        @endif
                    @endforeach
                    @if(request('user') != null)
                        <div class="mr-2 btn-group">
                            @if($selectedUser->status == UserStatus::ACTIVE)
                                <x-button color="danger" modal="deactivate-user">  Deaktivovať  používateľa {{$selectedUserName}}</x-button>
                            @else
                                <x-button color="danger" modal="activate-user">Aktivovať používateľa {{$selectedUserName}}</x-button>
                            @endif
                        </div>
                    @endif
                </x-content-box>
            </div>

        @endif

        <div class="{{$isAdmin ? 'col-md-8' : 'col-md'}}">
            <x-content-box title="Pracovné cesty">

                <form method="GET" action="{{ route('trip.index') }}">
                    <input type="hidden" name="filter" value="{{ request('filter') }}">
                    <input type="hidden" name="user" value="{{ request('user') }}">
                    <input type="hidden" name="inactive" value="{{ request('inactive') }}">
                    <x-content-section title="Usporiadať cesty podľa">
                        <div class="form-row">
                            <div class="col-md-6 col-12">
                                <select
                                    class="custom-select"
                                    name="sort"
                                    id="sort"
                                    onchange="this.form.submit();"
                                >
                                    <option value="date_created" {{ request('sort') == 'date_created' ? 'selected' : '' }}>Najnovšie</option>
                                    <option value="date_start" {{ request('sort') == 'date_start' ? 'selected' : '' }}>Dátum začiatku</option>
                                    <option value="place" {{ request('sort') == 'place' ? 'selected' : '' }}>Miesto</option>
                                    <option value="sofia_id" {{ request('sort') == 'sofia_id' ? 'selected' : '' }}>Identifikátor</option>
                                </select>
                            </div>
                        </div>
                    </x-content-section>
                </form>

                @forelse ($trips as $trip)
                    @php
                        $user = $trip->user;
                        $fullName = $user->first_name.' '.$user->last_name;
                        $sofiaId = $trip->sofia_id == null ? '0000' : $trip->sofia_id;
                        $dates = $trip->datetime_start->format('d.m.Y').' - '.$trip->datetime_end->format('d.m.Y');
                    @endphp
                    <x-trip-list-item :trip="$trip"/>
                    @empty
                        <p>Zoznam ciest je momentálne prázdny.</p>
                @endforelse

                <div class="d-flex justify-content-end">
                    {{$trips->links()}}
                </div>
            </x-content-box>
        </div>
    </div>

</x-layout>
