@php
    $trips = [
        (object) ['id' => 1, 'number' => 0712, 'start-date' => '02.12.2023', 'end-date' => '05.12.2023', 'state' => 0],
        (object) ['id' => 2, 'number' => 0715, 'start-date' => '20.12.2023', 'end-date' => '25.12.2023', 'state' => 0]
    ];
@endphp

<x-layout>
    <div class="mb-4">
        <x-link-button href="/trips/create">Pridať tuzemskú cestu</x-link-button>
        <x-link-button href="/trips/create">Pridať zahraničnú cestu</x-link-button>
        <x-button event="open-add-users">Pridať používateľov</x-button>
        <x-button event="open-spp-manager">ŠPP prvky</x-button>
    </div>

    <x-modals.add-user/>
    <x-modals.spp-manager/>

    <div class="row">
        <x-overview class="col-md-4"/>

        <x-content-box title="Pracovné cesty" class="col-md-8">
            @foreach($trips as $trip)
                <x-content-item :id="$trip->id" :date="$trip->{'start-date'}" :state="$trip->state" > {{ $trip->number }} </x-content-item>
            @endforeach
        </x-content-box>
    </div>



    @guest
        <p>Pre zobrazenie pracovných ciest a ich pridávanie či úpravu sa, prosím, prihláste.</p>
    @endguest
</x-layout>
