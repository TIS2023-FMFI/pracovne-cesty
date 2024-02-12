@if(session()->has('message'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
         class="fixed-top bg-secondary text-white px-3 py-2">
        <p>{{ session('message') }}</p>
    </div>
@endif

@if(session()->has('warning'))
    <div x-data="{ show: true }" x-show="show"
         class="fixed-top bg-secondary text-white px-3 py-2">
        <div class="flex justify-between">
            <p>{{ session('warning') }}</p>
            <button @click="show = false">Rozumiem</button>
        </div>
    </div>
@endif
