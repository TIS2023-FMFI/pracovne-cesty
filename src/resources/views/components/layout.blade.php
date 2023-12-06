@php
    $transportations = array("car", "bus", "plane", "train");
@endphp

<div>
    <x-header>
        Prihlásený ako: Zdenka Slobodová
    </x-header>

    <x-content-box title="Pracovné cesty" style="width: 50%">
        <x-content-item content="Prvá cesta" date="21.08.-23.08.2023" :state=0 ></x-content-item>
        <x-content-item content="Druhá cesta" date="21.08.-23.08.2023" :state=0 ></x-content-item>
        <x-content-item content="Tretia cesta" date="21.08.-23.08.2023" :state=0 ></x-content-item>
    </x-content-box>

    <x-overview/>

    <div>
        <x-simple-input name="name" type="text" label="Meno:"/>
        <x-simple-input name="surname" type="text" label="Priezvisko:" />
        <x-simple-input name="date1" type="date" label="Dátum:"/>
        <x-simple-input name="time1" type="time" label="Čas:"/>
        <x-simple-input name="refundation" type="checkbox" label="Mám záujem o refundáciu"/>
        <x-dropdown-input name="transportation" label="Dopravný prostriedok" :values="$transportations" selected="train"/>
    </div>
</div>
