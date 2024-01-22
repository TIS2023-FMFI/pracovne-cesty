@php
    use App\Models\BusinessTrip;
    use App\Models\User;

    $trips = BusinessTrip::paginate(15);
    $users = User::all();
@endphp

<x-layout>
    <div class="mb-4">
        <x-link-button href="/trips/create">Pridať pracovnú cestu</x-link-button>
        <x-button color="danger" event="open-add-users">Pridať používateľov</x-button>
        <x-link-button color="danger" href="/spp">ŠPP prvky</x-link-button>
    </div>

    <x-modals.add-user/>

    <div class="row">
        <div class="col-md-4">
            <x-content-box title="Prehľad">
                <x-overview-item content="Najnovšie" ref="/?filter=newest"/>
                <x-overview-item content="Nepotvrdené" ref="/?filter=unconfirmed"/>
                <x-overview-item content="Nevyúčtované" ref="/?filter=unaccounted"/>

                @foreach($users as $user)
                    <x-overview-item :content="$user->first_name.' '.$user->last_name" :ref="'/?user='.$user->id"/>
                @endforeach
            </x-content-box>
        </div>


        <div class="col-md-8">
            <x-content-box title="Pracovné cesty" >
                @foreach($trips as $trip)
                    @php
                        $user = $trip->user;
                        $fullName = $user->first_name.' '.$user->last_name;
                        $sofiaId = $trip->sofia_id == null ? '0000' : $trip->sofia_id;
                        $dates = $trip->datetime_start->format('d.m.Y').'-'.$trip->datetime_end->format('d.m.Y');
                    @endphp
                    <x-content-item :id="$trip->id" :sofia-id="$sofiaId" :state="$trip->state" :user="$fullName" :place="$trip->place" :purpose="$trip->tripPurpose->name" :date="$dates"/>
                @endforeach

                <div class="d-flex justify-content-end">
                    {{$trips->links()}}
                </div>
            </x-content-box>
        </div>
    </div>

</x-layout>
