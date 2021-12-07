@extends('layouts.app')

@section('content')
    <div class="row chat-row">
        <div class="col-md-3">
            <div class="users">
                <h5>freinds</h5>
                <ul class="list-group list-chat-item">
                    @forelse($users as $userList)

                        <li class="chat-user-list">
                            <a href="{{ route('message.conversation', $userList->id) }}">
                                <div class="chat-image">
                                    <img class="chat-photo" src="{{ asset('/img/users/' . $userList->personalImage) }}"
                                        alt="">
                                    <i class="fa fa-circle user-status-icon user-icon-{{ $userList->id }}"
                                        title="away"></i>
                                </div>
                                <div class="chat-name font-weight-bold">
                                    {{ $userList->name }}
                                </div>

                            </a>
                        </li>
                    @empty
                        .... please add more friends
                    @endforelse

                </ul>
            </div>
        </div>
        <div class="col-md-9 chat-section">
            <div class="chat-header">
                <div class="chat-image">
                    <img class="chat-photo" src="{{ asset('/img/users/' . $group->GroupImage) }}" alt="">
                </div>

                <div class="chat-name font-weight-bold">
                    {{ $group->name }}

                </div>
            </div>

            <div class="chat-body" id="chatBody">
                @foreach ($messages as $message)
                    <div class="message-listing" id="messageWrapper">
                        <div class="row message align user-info">
                            <div class="chat-image">
                                <img class="chat-photo"
                                    src="{{ asset('/img/users/' . $message->sender->personalImage) }}" alt="">
                            </div>
                            <div class="chat-name font-weight-bold">

                                <span class="text-gray-500 small time">{{ $message->sender->name }} -
                                    {{ $message->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="col-md12 message-contnet">
                            <div class="message-text">
                                {{ $message->content }}
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

            <div class="chat-box">
                <div class="bg-white chat-input" id="chatInput" contenteditable="">

                </div>

                <div class="chat-input-toolbar">
                    <button title="Add File" class="btn btn-light btn-sm btn-file-upload">
                        <i class="fa fa-paperclip"></i>
                    </button> |

                    <button title="Bold" class="btn btn-light btn-sm tool-items"
                        onclick="document.execCommand('bold', false, '');">
                        <i class="fa fa-bold tool-icon"></i>
                    </button>

                    <button title="Italic" class="btn btn-light btn-sm tool-items"
                        onclick="document.execCommand('italic', false, '');">
                        <i class="fa fa-italic tool-icon"></i>
                    </button>
                </div>
            </div>
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
            let $chatInput = $('.chat-input');
            let $chatInputToolbar = $('.chat-input-toolbar');
            let $chatBody = $('.chat-body');
            let $messageWrapper = $('#messageWrapper');
            let ip_iddress = '127.0.0.1';
            let socket_port = '8005';
            let socket = io(ip_iddress + ':' + socket_port);
            socket.on('connect', function() {
                socket.emit('user_connected', "{{ auth()->user()->id }}");
            });
            socket.on('updateUserStatus', (data) => {
                let $userStatusIcon = $(".user-icon-" + "{{ $userList->id }}");
                $userStatusIcon.removeClass('text-succuess');
                $userStatusIcon.attr('title', 'Away');
                $.each(data, function(key, val) {
                    if (val !== null && val !== 0) {
                        $userStatusIcon.addClass('text-succuess');
                        $userStatusIcon.attr('title', 'Active');
                    }
                });
                $chatInput.keypress(function(e) {
                    let message = $(this).html();
                    if (e.which === 13 && !e.shiftKey && message !== null) {
                        $chatInput.html('');
                        sendMessage(message);
                        return false;
                    }
                });

                function sendMessage(message) {
                    let url = "{{ route('group.message.store', $group->id) }}";
                    let form = $(this);
                    let formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('content', message);
                    $.ajax({
                        url: url,
                        type: 'post',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: "JSON",
                        success: function(res) {
                            if (res.success) {
                                console.log(res);
                            }
                        }

                    });
                }
            });
        </script>
    @endpush
@endsection
