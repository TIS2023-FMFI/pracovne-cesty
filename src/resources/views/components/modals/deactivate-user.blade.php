@php
    use App\Models\User;

    $selectedUserId = request()->query('user');
    $selectedUser = $selectedUserId ? User::find($selectedUserId) : null;
    $selectedUserName = $selectedUser ? $selectedUser->first_name.' '.$selectedUser->last_name : '';
@endphp
<x-modal title="Deaktivovať používateľa" name="deactivate-user">
    <form action="{{ route('user.deactivate') }}" method="POST">
        @csrf
        <x-content-section>
            <p>Naozaj chcete deaktivovať používateľa <b>{{$selectedUserName}}</b>?</p>
            <input type="hidden" name="user" value="{{ request('user') }}">
        </x-content-section>
        <div class="d-flex justify-content-end">
            <x-button>Deaktivovať</x-button>
        </div>
    </form>
</x-modal>
