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
        $users = User::where('id', '!=', Auth::user()->id)->get();
        $messages = message::whereHas(
            'UserMessages',
            function ($q) use ($user) {
                $q->where('receiver_id', $user->id)->where(
                    'sender_id',
                    Auth::user()->id
                )->with(['sender', 'receiver']);
            }
        )->paginate(4);

        return view('message.conversation', get_defined_vars());
    }
    public function store(MessageRequest $r)
    {
        $r->merge(['sender_id' => Auth::user()->id]);
        $r->type ?? $r->merge(['type' => $r->type]);
        $message = message::create(['content' => $r->content]);
        $sender_id = Auth::id();
        $receiver_id = $r->receiver_id;
        if ($message) {
            try {
                $message->Users()->attach($r->sender_id, ['receiver_id' => $r->receiver_id]);
                $sender = User::where('id', '=', $sender_id)->first();
                $data = [];
                $data['sender_id'] = $sender_id;
                $data['sender_name'] = $sender->name;
                $data['receiver_id'] = $r->receiver_id;
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
