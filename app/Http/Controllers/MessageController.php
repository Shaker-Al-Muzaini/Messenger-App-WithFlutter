<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Participant;   
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public  function  index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $user = Auth::user();
                $chats= $user->conversations()->with([
                'lastMessage',
                'participants'=>function($builder) use($user){
            $builder->where('id','<>',$user->id);
            }])->get();

        $friends = User::where('id', '<>', $user->id)
            ->orderBy('name')
            ->paginate();
        return view('messenger', [
            'friends' => $friends,
        ]);
    }

}
