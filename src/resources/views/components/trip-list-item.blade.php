@use('App\Enums\TripState')
@props(['trip'])

<a
    href="{{ route('trip.edit', ['trip' => $trip->id]) }}"
    class="text-decoration-none text-dark border-bottom">
    <div class="container my-2 py-2 border-bottom">
        <div class="row">
            <div class="col-7">
                <div class="row">
                    <div class="col-12">
                        <b>{{ $trip->sofia_id }}</b>
                    </div>
                    <div class="col-12">
                        @if(Auth::user()->hasRole('admin'))
                            {{ $trip->user->first_name.' '.$trip->user->last_name.': '}}
                        @endif
                            {{ $trip->place }}
                    </div>
                    <div class="col-12">
                        {{ $trip->tripPurpose->name }}
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="row">
                    <div class="col-12">
                        {{ $trip->datetime_start->format('d.m.Y').' - '.$trip->datetime_end->format('d.m.Y') }}
                    </div>
                    @if($trip->state == TripState::CONFIRMED)
                        <div class="col-12 text-danger small">
                            {{ "Správu podať do: " . $trip->datetime_end->copy()->addDays(7)->format('d.m.Y')}}
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-1">
                <x-state-icon :state="$trip->state"/>
            </div>
        </div>
    </div>
</a>
