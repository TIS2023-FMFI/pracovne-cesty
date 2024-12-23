@php
    use App\Models\User;

    $selectedUserId = request()->query('user');
    $selectedUser = $selectedUserId ? User::find($selectedUserId) : null;
    $selectedUserName = $selectedUser ? $selectedUser->first_name.' '.$selectedUser->last_name : '';
@endphp
<x-modal title="Aktivovať používateľa" name="activate-user">
    <form action="{{ route('user.activate') }}" method="POST">
        @csrf
        <x-content-section>
            <p>Naozaj chcete aktivovať používateľa <b>{{$selectedUserName}}</b>?</p>
            <input type="hidden" name="user" value="{{ request('user') }}">
        </x-content-section>
        <div class="d-flex justify-content-end">
            <x-button>Aktivovať</x-button>
        </div>
    </form>
</x-modal>
