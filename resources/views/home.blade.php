@extends('layouts.app')

@section('content')
    <div class="row chat-row">
        <div class="col-md-3">
            <div class="users">
                <h5>users</h5>
                <ul class="list-group list-chat-item">
                    @forelse($users as $user)
                        <li class="chat-user-list">
                            <a href="{{ route('message.conversation', $user->id) }}">
                                <div class="chat-image">
                                    <img class="chat-photo" src="{{ asset('img/users/' . $user->personalImage) }}"
                                        alt="">
                                    <i class="fa fa-circle user-status-icon user-icon-{{ $user->id }}" title="away"></i>
                                </div>
                                <div class="chat-name font-weight-bold">
                                    {{ $user->name }}
                                </div>

                            </a>
                        </li>
                    @empty
                        .... please add more friends
                    @endforelse

                </ul>
            </div>
        </div>
        <div class="col-md-9">
            <h1>
                Message Section
            </h1>

            <p class="lead">
                Select user from the list to begin conversation.
            </p>
        </div>
    </div>
    <div class=" row chat-row">
        <div class="col-md-3">
            <div class="users">
                <h5>groups</h5>
                <ul class="list-group list-chat-item">
                    @forelse($groups as $group)
                        <li class="chat-user-list">
                            <a href="{{ route('group.message.conversation', $group->id) }}">
                                <div class="chat-image">
                                    <img class="chat-photo" src="{{ asset('img/users/' . $group->GroupImage) }}"
                                        alt="">
                                </div>
                                <div class="chat-name font-weight-bold">
                                    {{ $group->name }}
                                </div>

                            </a>
                        </li>
                    @empty
                        .... please join to more groups
                    @endforelse

                </ul>
            </div>
        </div>
        <div class="col-md-9">
            <h1>
                Groups Section
            </h1>

            <p class="lead">
                Select Group from the list to begin conversation.
            </p>
        </div>
    </div>
    @push('scripts')
        <script>
            $(function() {
                let ip_address = '127.0.0.1';
                let socket_port = '8005';
                let socket = io(ip_address + ':' + socket_port);

                socket.on('connect', function() {
                    socket.emit('user_connected', "{{ auth()->user()->id }}");
                });
                socket.on('updateUserStatus', (data) => {
                    console.log(data);
                    let $userStatusIcon = $(".user-status-icon");
                    $userStatusIcon.removeClass('text-succuess');
                    $userStatusIcon.attr('title', 'Away');

                    $.each(data, function(key, val) {
                        if (val !== null && val !== 0) {
                            console.log(key);
                            let $userIcon = $(".user-icon-" + key);
                            $userIcon.addClass('text-succuess');
                            $userIcon.attr('title', 'Active');
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
