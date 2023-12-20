<x-layout>
    <x-content-box title="Detaily pracovnej cesty" style="width: 70%">
        <x-content-section title="Osobné údaje">
            <p>Meno: {{ $trip->user->first_name ?? '' }}</p>
            <p>Priezvisko: {{ $trip->user->last_name ?? '' }}</p>
            <p>Tituly: {{ $trip->user->academic_degrees ?? '' }}</p>
            <p>Bydlisko: {{ $trip->user->address ?? '' }}</p>
            <p>Osobné číslo: {{ $trip->user->personal_number ?? '' }}</p>
            <p>Pracovisko: {{ $trip->user->department ?? '' }}</p>
            <p>Číslo účtu: {{ $trip->account_number ?? '' }}</p>
        </x-content-section>

        <div>
            <p><strong>Začiatok cesty:</strong></p>
            <p>Miesto: {{ $trip->start_location ?? '' }}</p>
            <p>Dátum: {{ $trip->start_date ?? '' }}</p>
            <p>Čas: {{ $trip->start_time ?? '' }}</p>
        </div>

        <div>
            <p><strong>Koniec cesty:</strong></p>
            <p>Miesto: {{ $trip->end_location ?? '' }}</p>
            <p>Dátum: {{ $trip->end_date ?? '' }}</p>
            <p>Čas: {{ $trip->end_time ?? '' }}</p>
        </div>

        <x-content-section title="Cieľ cesty">
            <p>Miesto: {{ $trip->location ?? '' }}</p>
            <p>Štát: {{ $trip->country ?? '' }}</p>
            <p>Dopravný prostriedok: {{ $trip->transportation ?? '' }}</p>
            <p>Účel cesty: {{ $trip->purpose ?? '' }}</p>
            <p>Špecifikácia: {{ $trip->specification ?? '' }}</p>
            <p>Link na udalosť: {{ $trip->url ?? '' }}</p>
            <p>Prínos pre fakultu: {{ $trip->acquisition ?? '' }}</p>
        </x-content-section>

        <x-content-section title="Financovanie">
            <p>ŠPP prvok 1: {{ $trip->spp1 ?? '' }}</p>
            <p>ŠPP prvok 2: {{ $trip->spp2 ?? '' }}</p>
            <p>Predpokladaný dátum vrátenia peňazí: {{ $trip->refundation_date ?? '' }}</p>
        </x-content-section>

        <x-content-section title="Úhrada konferenčného poplatku">
            <p>Názov organizácie: {{ $trip->organization_name ?? '' }}</p>
            <p>IČO: {{ $trip->ico ?? '' }}</p>
            <p>Adresa organizácie: {{ $trip->organization_address ?? '' }}</p>
            <p>Číslo účtu organizácie: {{ $trip->organization_account_number ?? '' }}</p>
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
