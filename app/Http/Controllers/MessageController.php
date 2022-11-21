<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public  function  index($id =null)
    {
        $user = Auth::user();
        $friends = User::where('id', '<>', $user->id)
            ->orderBy('name')
            ->paginate();
        $chats= $user->conversations()->with([
            'lastMessage',
            'participants'=>function($builder) use($user){
                $builder->where('id','<>',$user->id);
            }])->get();
        $messages=[];
        $activeChat=null;
        if($id){
            $activeChat=$chats->where('id',$id)->first();
            $messages=$activeChat->messages()->with('user')->paginate();
        }
        return view('messenger',[
            'friends' => $friends,
            'chats'=>$chats,
            'messages'=>$messages,
            'activeChat'=>$activeChat,
        ]);
    }

}
