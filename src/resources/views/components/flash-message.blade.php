@if(session()->has('message'))
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 3000)"
        x-show="show"
        x-cloak
        class="fixed-top bg-secondary text-white px-3 py-2">

        <p>
            {{ session('message') }}
        </p>
    </div>
@endif
