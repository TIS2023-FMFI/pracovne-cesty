<a
    href="trips/{{ $id }}/edit"
    class="text-decoration-none text-dark border-bottom">
    <div class="container my-2 py-2 border-bottom">
        <div class="row">
            <div class="col-7">
                <div class="row">
                    <div class="col-12">
                        <b>{{ $sofiaId }}</b>
                    </div>
                    <div class="col-12">
                        {{ $user }}: {{ $place }}
                    </div>
                    <div class="col-12">
                        {{ $purpose }}
                    </div>
                </div>
            </div>
            <div class="col-4">
                {{ $date }}
            </div>
            <div class="col-1">
                <div style="width: 30px; height: 30px;">
                    <i class="fa-solid fa-{{ $getIcon() }}"></i>
                </div>
            </div>
        </div>
    </div>
</a>
