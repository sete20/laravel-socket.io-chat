<?php

namespace App\Http\Controllers;

use App\Events\PrivateMessageEvent;
use App\Http\Requests\MessageRequest;
use App\message;
use App\User;
use App\UserMessage;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function conversation(User $user)
    {
        $users
            = \Auth::user()->friends;
        $messages = message::whereSenderId(\Auth::user()->id)->where('receiver_id', $user->id)
            ->orWhere('sender_id', $user->id)->orWhere('receiver_id', \Auth::user()->id)
            ->get();

        return view('message.conversation', get_defined_vars());
    }
    public function store(MessageRequest $r)
    {
        $r->merge(['sender_id' => Auth::user()->id]);
        $r->merge(['type' => 2]);
        $r->type ?? $r->merge(['content_type' => $r->type]);

        $message = message::create([
            'content' => $r->content,
            'content_type' => $r->content_type ? $r->content_type : 1,
            'sender_id' => $r->sender_id,
            'receiver_id' => $r->receiver_id,
            'type' => $r->type,
        ]);
        if ($message) {
            try {
                $data = [];
                $data['sender_id'] = $message->sender_id;
                $data['sender_name'] = $message->sender->name;
                $data['receiver_personal_image'] = asset('img/users/' . $message->receiver->personalImage);
                $data['sender_personal_image'] = asset('img/users/' . $message->sender->personalImage);
                $data['receiver_id'] = $message->receiver_id;
                $data['receiver_name'] = $message->receiver->name;
                $data['content'] = $message->content;
                $data['created_at'] = $message->created_at;
                $data['message_id'] = $message->id;
                event(new PrivateMessageEvent($data));
                return response()->json(
                    [
                        'data' => $data,
                        'success' => true,
                        'message' => 'Message Sent Successfully'
                    ]
                );
            } catch (\Exception $e) {
                return $e;
                $message->delete();
            }
        }
    }
}
