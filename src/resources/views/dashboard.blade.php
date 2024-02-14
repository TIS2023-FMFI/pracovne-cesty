@php
    use App\Models\User;

    $isAdmin = Auth::user()->hasRole('admin');

    $selectedUserId = request()->query('user');
    $selectedUser = $selectedUserId ? User::find($selectedUserId) : null;
    $selectedUserName = $selectedUser ? $selectedUser->first_name.' '.$selectedUser->last_name : '';
@endphp

<x-layout>
    <div class="mb-3 btn-toolbar" role="toolbar">
        <div class="mr-2 btn-group">
            <x-link-button href="/trips/create">Pridať pracovnú cestu
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
            <x-link-button color="danger" href="/spp">ŠPP prvky</x-link-button>
        </div>

        @endif
    </div>

    @if($isAdmin)
        <x-modals.add-user/>
    @endif

    <div class="row">
        @if($isAdmin)
            <div class="col-md-4">
                <x-content-box title="Prehľad">
                    <x-overview-item ref="/?filter=newest"><b>Najnovšie</b></x-overview-item>
                    <x-overview-item ref="/?filter=unconfirmed"><b>Nepotvrdené</b></x-overview-item>
                    <x-overview-item ref="/?filter=unaccounted"><b>Nevyúčtované</b></x-overview-item>

                    <div class="my-3"></div>

                    @foreach($users as $user)
                        <x-overview-item :ref="'?user='.$user->id">{{ $user->first_name.' '.$user->last_name }}</x-overview-item>
                    @endforeach
                </x-content-box>
            </div>
        @endif

        <div class="{{$isAdmin ? 'col-md-8' : 'col-md'}}">
            <x-content-box title="Pracovné cesty">
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
