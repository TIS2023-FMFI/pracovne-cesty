@php
    $isAdmin = Auth::user()->hasRole('admin');
@endphp

<x-layout>
    <div class="mb-3">
        @php
            $userId = request()->query('user');
        @endphp
        <x-link-button href="{{ $userId ? route('trip.create', ['user' => $userId]) : route('trip.create') }}">Pridať pracovnú cestu</x-link-button>

        @if($isAdmin)
            <x-button color="danger" modal="add-users">Pridať používateľov</x-button>
            <x-link-button color="danger" href="/spp">ŠPP prvky</x-link-button>
        @endif
    </div>

    @if($isAdmin)
        <x-modals.add-user/>
    @endif

    <div class="row">

        @if($isAdmin)
            <div class="col-md-4">
                <x-content-box title="Prehľad">
                    <x-overview-item content="Najnovšie" ref="/?filter=newest"/>
                    <x-overview-item content="Nepotvrdené" ref="/?filter=unconfirmed"/>
                    <x-overview-item content="Nevyúčtované" ref="/?filter=unaccounted"/>

                    @foreach($users as $user)
                        <x-overview-item :content="$user->first_name.' '.$user->last_name" :ref="'?user='.$user->id"/>
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
                        $dates = $trip->datetime_start->format('d.m.Y').'-'.$trip->datetime_end->format('d.m.Y');
                    @endphp
                    <x-content-item :id="$trip->id" :sofia-id="$sofiaId" :state="$trip->state" :user="$fullName" :place="$trip->place" :purpose="$trip->tripPurpose->name" :date="$dates"/>
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
