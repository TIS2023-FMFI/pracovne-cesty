@if(session()->has('message'))
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 3000)"
        x-show="show"
        class="fixed-top row no-gutters my-2">
        <div class="col-lg-5 col-md-12 m-auto">
            <div class="alert alert-dark fade show" role="alert">
                <p>{{ session('message') }}</p>
            </div>
        </div>
    </div>
@endif

@if(session()->has('warning'))
    <div
        x-data="{ show: true }"
        x-show="show"
        class="fixed-top row no-gutters">
        <div class="col-lg-5 col-md-12 m-auto">
            <div class="alert alert-dark fade show" role="alert">
                <p>{{ session('warning') }}</p>
                <div class="d-flex justify-items-end">
                    <button @click="show = false">Rozumiem</button>
                </div>
            </div>
        </div>
    </div>
@endif
