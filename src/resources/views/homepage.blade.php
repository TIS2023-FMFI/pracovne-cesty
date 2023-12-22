@php
    $trips = [
        (object) ['id' => 1, 'number' => 0712, 'start-date' => '02.12.2023', 'end-date' => '05.12.2023', 'state' => 0],
        (object) ['id' => 2, 'number' => 0715, 'start-date' => '20.12.2023', 'end-date' => '25.12.2023', 'state' => 0]
    ];
@endphp

<x-layout>
    <x-link-button href="/trips/create">Pridať tuzemskú cestu</x-link-button>
    <x-link-button href="/trips/create">Pridať zahraničnú cestú</x-link-button>

    <button x-data="{}" @click="$dispatch('open-add-users')">Pridať používateľa</button>
    <x-modals.add-user></x-modals.add-user>

    <button x-data="{}" @click="$dispatch('open-spp-manager')">ŠPP prvky</button>
    <x-modals.spp-manager></x-modals.spp-manager>

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
