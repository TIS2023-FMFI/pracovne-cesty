@php
    use App\Models\BusinessTrip;
    use App\Models\User;
    $trips = BusinessTrip::all();
    $users = User::all();
@endphp

<x-layout>
    <div class="mb-4">
        <x-link-button href="/trips/create">Pridať tuzemskú cestu</x-link-button>
        <x-link-button href="/trips/create">Pridať zahraničnú cestu</x-link-button>
        <x-button color="btn-danger" event="open-add-users">Pridať používateľov</x-button>
        <x-button color="btn-danger" event="open-spp-manager">ŠPP prvky</x-button>
        <x-button event="open-register-form">Registrácia</x-button>
    </div>

    <x-modals.add-user/>
    <x-modals.spp-manager/>
    <x-modals.register/>


    <div class="row">
        <x-content-box title="Prehľad" class="col-md-4">
            <x-overview-item content="Najnovšie"/>
            <x-overview-item content="Nepotvrdené"/>
            <x-overview-item content="Nevyúčtované"/>
            <x-overview-item/>

            @foreach($users as $user)
                <x-overview-item :content="$user->first_name.' '.$user->last_name" :reference="'users/'.$user->id"></x-overview-item>
            @endforeach


        </x-content-box>


        <x-content-box title="Pracovné cesty" class="col-md-8">
            @foreach($trips as $trip)
                @php
                    $user = $trip->user;
                    $fullName = $user->first_name.' '.$user->last_name;
                @endphp
                <x-content-item :id="$trip->id" :sofia-id="$trip->sofia_id == null ? '0000' : $trip->sofia_id" :state="$trip->state" :user="$fullName" :place="$trip->place" :purpose="$trip->tripPurpose->name" :date="$trip->datetime_start"/>
            @endforeach
        </x-content-box>
    </div>




    @guest
        <p>Pre zobrazenie pracovných ciest a ich pridávanie či úpravu sa, prosím, prihláste.</p>
    @endguest
</x-layout>
