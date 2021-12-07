<?php

namespace App\Http\Controllers;

use App\Events\GroupMessageEvent;
use App\Events\PrivateMessageEvent;
use App\Group;
use App\Http\Requests\GroupRequest;
use App\Http\Requests\MessageRequest;
use App\message;
use App\User;
use App\UserMessage;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function conversation(User $user)
    {
        $users
            = \Auth::user()->friends;
        $messages = message::whereSenderId(\Auth::user()->id)->where('receiver_id', $user->id)
            ->orWhere('sender_id', $user->id)->where('receiver_id', \Auth::user()->id)
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

    public function group_conversation(Group $group)
    {
        $messages = $group->messages;
        $users = \Auth::user()->friends;
        $groups = \Auth::user()->groups->where('status', 2);
        return view('message.group_conversation', get_defined_vars());;
    }
    public function group_store(Group $group, GroupRequest $r)
    {
        $r->merge(['sender_id' => Auth::user()->id]);
        $r->merge(['type' => 2]);
        $r->type ?? $r->merge(['content_type' => $r->type]);
        $message = message::create([
            'content' => $r->content,
            'content_type' => $r->content_type ? $r->content_type : 1,
            'sender_id' => $r->sender_id,
            'group_id' => $group->id,
            'type' => $r->type,
        ]);

        if ($message) {
            try {
                $data = [];
                $data['group_id'] = $message->group_id;
                $data['sender_name'] = $message->group->name;
                $data['group_image'] = asset('img/users/' . $message->group->GroupImage);
                $data['sender_personal_image'] = asset('img/users/' . $message->sender->personalImage);
                $data['group_id'] = $message->group->id;
                $data['group_name'] = $message->group->name;
                $data['content'] = $message->content;
                $data['created_at'] = $message->created_at;
                $data['message_id'] = $message->id;
                event(new GroupMessageEvent($data));
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
