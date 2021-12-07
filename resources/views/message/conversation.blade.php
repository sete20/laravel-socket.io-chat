@extends('layouts.app')

@section('content')
    <div class="row chat-row">
        <div class="col-md-3">
            <div class="users">
                <h5>users</h5>
                <ul class="list-group list-chat-item">
                    @forelse($users as $userList)
                        <li class="chat-user-list @if ($userList->id == $user->id)  active @endif">
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
                    <img class="chat-photo" src="{{ asset('/img/users/' . $user->personalImage) }}" alt="">
                </div>

                <div class="chat-name font-weight-bold">
                    {{ $user->name }}
                    <i class="fa fa-circle  user-icon-{{ $user->id }} " title="away"
                        id="userStatusHead{{ $user->id }}"></i>
                </div>
            </div>

            <div class="chat-body" id="chatBody">
                @foreach ($messages as $message)
                    @if ($message->sender_id !== Auth::user()->id)

                        <div class="message-listing" id="messageWrapper">
                            <div class="row message align user-info">
                                <div class="chat-image">
                                    <img class="chat-photo" src="{{ asset('/img/users/' . $user->personalImage) }}"
                                        alt="">
                                </div>
                                <div class="chat-name font-weight-bold">
                                    <span class="text-gray-500 small time">{{ $message->sender->name }}</span>
                                    <br>
                                    <span class="text-gray-500 small time">{{ $message->created_at }}</span>

                                </div>
                            </div>
                            <div class="col-md12 message-contnet">
                                <div class="message-text">
                                    {{ $message->content }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="message-listing recever" id="messageWrapper">
                            <div class="row message align user-info">
                                <div class="chat-image">
                                    <img class="chat-photo" src="{{ asset('/img/users/' . $user->personalImage) }}"
                                        alt="">
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="chat-name font-weight-bold">
                                        <span class="text-gray-500 small time">{{ $message->sender->name }}</span>
                                        <br>
                                        <span class="text-gray-500 small time">{{ $message->created_at }}</span>

                                    </div>
                                </div>
                                <div class="col-md12 message-contnet">
                                    <div class="message-text">
                                        {{ html_entity_decode($message->content) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endif
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



    @push('scripts')
        <script>
            // message.conversation

            $(function() {
                let $chatInput = $('.chat-input');
                let $chatInputToolbar = $('.chat-input-toolbar');
                let $chatBody = $('.chat-body');
                let receiver_id = "{{ $user->id }}";
                let $messageWrapper = $('#messageWrapper');

                let ip_address = '127.0.0.1';
                let socket_port = '8005';
                let socket = io(ip_address + ':' + socket_port);


                socket.on('connect', function() {
                    socket.emit('user_connected', "{{ auth()->user()->id }}");
                });
                //end on connect
                socket.on('updateUserStatus', (data) => {
                    console.log(data);
                    let $userStatusIcon = $(".user-icon-" + "{{ $userList->id }}");
                    $userStatusIcon.removeClass('text-succuess');
                    $userStatusIcon.attr('title', 'Away');
                    let $selectedUser = $('.user-status-head-' + {{ $user->id }});
                    $.each(data, function(key, val) {
                        if (val !== null && val !== 0) {
                            console.log(key);
                            let $userIcon = $(".user-icon-" + key);
                            $selectedUser.addClass('text-succuess');
                            $selectedUser.attr('title', 'Active');
                            $userIcon.addClass('text-succuess');
                            $userIcon.attr('title', 'Active');
                        }
                    });
                });
                // end update users status

                $chatInput.keypress(function(e) {
                    let message = $(this).html();
                    if (e.which === 13 && !e.shiftKey) {
                        $chatInput.html('');
                        sendMessage(message);
                        return false;
                    }
                });
                //end on key press

                function sendMessage(message) {
                    let url = "{{ route('message.store') }}";
                    let form = $(this);
                    let formData = new FormData();
                    let token = "{{ csrf_token() }}";
                    formData.append('content', message);
                    formData.append('_token', token);
                    formData.append('receiver_id', receiver_id);
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'JSON',
                        success: function(response) {
                            if (response.success) {
                                console.log(response.data);
                                appendToSender(response.data.content);
                            }
                        }
                    });
                } //end send message

                function appendToSender(message) {
                    let name = "{{ auth()->user()->name }}";
                    let $image = "{{ asset('img/users/' . auth()->user()->personalImage) }}";
                    userInfo = '<div class="col-md-12 user-info">\n' +
                        '<div class="chat-image">\n' +
                        '<img class="chat-photo" src="{{ asset('img/users/' . auth()->user()->personalImage) }}">\n' +
                        '</div>\n' +
                        '\n' +
                        '<div class="chat-name font-weight-bold">\n' +
                        name +
                        '<span class="text-gray-500 small time" title="' + getCurrentDateTime() + '">\n' +
                        getCurrentTime() + '</span>\n' +
                        '</div>\n' +
                        '</div>\n';
                    let messageContent = '<div class="col-md-12 message-content">\n' +
                        '                            <div class="message-text">\n' + message +
                        '                            </div>\n' +
                        '                        </div>';
                    let newMessage = '<div class="mb-2 row message align-items-center">' +
                        userInfo + messageContent +
                        '</div>';
                    $messageWrapper.append(newMessage);

                } //end  append message to sender


                socket.on("private-channel:App\\Events\\PrivateMessageEvent", function(message) {
                    appendToReceiver(message)
                }); //end on event
                function appendToReceiver(message) {
                    let name = message.sender_name;
                    let image = message.personal_image;
                    userInfo = '<div class="col-md-12 user-info">\n' +
                        '<div class="chat-image">\n' +
                        '<img class="chat-photo" src="{{ asset('img/users/' . auth()->user()->personalImage) }}">\n' +
                        '</div>\n' +
                        '\n' +
                        '<div class="chat-name font-weight-bold">\n' +
                        name +
                        '<span class="text-gray-500 small time" title="' + dataFormat(message.created_at) + '">\n' +
                        timeFormat(message.created_at) + '</span>\n' +
                        '</div>\n' +
                        '</div>\n';
                    let messageContent = '<div class="col-md-12 message-content">\n' +
                        '                            <div class="message-text">\n' + message.content +
                        '                            </div>\n' +
                        '                        </div>';
                    let newMessage = '<div class="mb-2 row message align-items-center">' +
                        userInfo + messageContent +
                        '</div>';
                    $messageWrapper.append(newMessage);
                } //end  append message to Receiver
            });
        </script>
    @endpush
@endsection
