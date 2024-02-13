@props(['trip'])

<a
    href="trips/{{ $trip->id }}/edit"
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
                {{ $trip->datetime_start->format('d.m.Y').' - '.$trip->datetime_end->format('d.m.Y') }}
            </div>
            <div class="col-1">
                <div class="state-icon">
                    <i class="fa-solid fa-{{ $trip->state->icon() }}"></i>
                </div>
            </div>
        </div>
    </div>
</a>
