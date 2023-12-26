<x-layout>
    <x-content-box title="Detaily pracovnej cesty">
        <x-content-section title="Osobné údaje">
            <p>Meno: {{ $trip->user->first_name ?? '' }}</p>
            <p>Priezvisko: {{ $trip->user->last_name ?? '' }}</p>
            <p>Tituly: {{ $trip->user->academic_degrees ?? '' }}</p>
            <p>Bydlisko: {{ $trip->user->address ?? '' }}</p>
            <p>Osobné číslo: {{ $trip->user->personal_id ?? '' }}</p>
            <p>Pracovisko: {{ $trip->user->department ?? '' }}</p>
            <p>Číslo účtu: {{ $trip->iban ?? '' }}</p>
        </x-content-section>

        <div>
            <p><strong>Začiatok cesty:</strong></p>
            <p>Miesto: {{ $trip->place_start ?? '' }}</p>
            <p>Dátum a čas: {{ $trip->datetime_start ?? '' }}</p>
        </div>

        <div>
            <p><strong>Koniec cesty:</strong></p>
            <p>Miesto: {{ $trip->place_end ?? '' }}</p>
            <p>Dátum a čas: {{ $trip->datetime_end ?? '' }}</p>
        </div>

        <x-content-section title="Cieľ cesty">
            <p>Miesto: {{ $trip->place ?? '' }}</p>
            <p>Štát: {{ $trip->country ?? '' }}</p>
            <p>Dopravný prostriedok: {{ $trip->transport ?? '' }}</p>
            <p>Účel cesty: {{ $trip->trip_purpose ?? '' }}</p>
            <p>Špecifikácia: {{ $trip->purpose_details ?? '' }}</p>
            <p>Link na udalosť: {{ $trip->event_url ?? '' }}</p>
            <p>Prínos pre fakultu: {{ $trip->contribution ?? '' }}</p>
        </x-content-section>

        <x-content-section title="Financovanie">
            <p>ŠPP prvok 1: {{ $trip->spp_symbol ?? '' }}</p>
            <p>ŠPP prvok 2: {{ $trip->reimbursement_spp ?? '' }}</p>
            <p>Predpokladaný dátum vrátenia peňazí: {{ $trip->reimbursement_date ?? '' }}</p>
        </x-content-section>

        <x-content-section title="Úhrada konferenčného poplatku">
            <p>Názov organizácie: {{ $trip->organiser_name ?? '' }}</p>
            <p>IČO: {{ $trip->ico ?? '' }}</p>
            <p>Adresa organizácie: {{ $trip->organiser_address ?? '' }}</p>
            <p>Číslo účtu organizácie: {{ $trip->organiser_iban ?? '' }}</p>
            <p>Suma: {{ $trip->amount ?? '' }}</p>
        </x-content-section>

        <x-content-section>
            <form method="POST" action="/trips/{{ $trip->id }}/">
                @csrf
                <textarea id="note" name="note" rows="4" cols="50"></textarea>
                <x-form-button>Pridať poznámku</x-form-button>
            </form>
        </x-content-section>
    </x-content-box>
</x-layout>
