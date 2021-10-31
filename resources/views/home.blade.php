@extends('layouts.app')

@section('content')
<div class="row chat-row">
    <div class="col-md-3">
        <div class="users">
            <h5>users</h5>
            <ul class="list-group list-chat-item">
                @forelse($users as $user)
                         <li class="chat-user-list" >
                    <a href="#">
                        <div class="chat-image">
                            <div class="name-image ">
                         <img class="chat-photo" src="{{ asset($user->personalImage) }}" alt="">

                            </div>
                        </div>
                       {{  $user->name }}


                    </a>
                </li>
                @empty
                .... please add more friends
                @endforelse

            </ul>
        </div>
    </div>
        <div class="col-md-9">

    </div>
</div>
@push('scripts')

@endpush
@endsection
