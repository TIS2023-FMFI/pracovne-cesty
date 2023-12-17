@php
    $trips = [
        (object) ['id' => 1, 'number' => 0712, 'start-date' => '02.12.2023', 'end-date' => '05.12.2023', 'state' => 0],
        (object) ['id' => 2, 'number' => 0715, 'start-date' => '20.12.2023', 'end-date' => '25.12.2023', 'state' => 0]
    ];
@endphp

<x-layout>
{{--    @auth--}}
{{--        @auth('admin')--}}
            <x-overview/>
{{--        @endauth--}}

        <x-content-box title="Pracovné cesty">
            @foreach($trips as $trip)
                <x-content-item :id="$trip->id" :date="$trip->{'start-date'}" :state="$trip->state" > {{ $trip->number }} </x-content-item>
            @endforeach
        </x-content-box>

{{--    @endauth--}}

    @guest
        <p>Pre zobrazenie pracovných ciest a ich pridávanie či úpravu sa, prosím, prihláste.</p>
    @endguest
</x-layout>
