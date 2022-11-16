<?php

namespace App\Http\Controllers;
 
use App\Events\MessageCreate; 
use App\Models\Conversation;
use App\Models\Recipient; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class MessagesController extends Controller
{

    public function index($id)
    {
        $user =Auth::user();
       $conversation= $user->conversations()->findOrFail($id);
        return  $conversation->messages()->pagintate();
    }


    /**
     * @throws Throwable
     */
    public function store(Request $request)
    {
                $request->validate([
            'message'=>[
                'required',
                'string'
            ],
            'conversation_id'=>[
                'int',
                'exists:conversations,id',
                Rule::requiredIf( function() use ($request){
                    return !$request->input('user_id');
                }),
            ],
            'user_id'=>[
                'int',
                'exists:users,id',
                Rule::requiredIf( function() use ($request){
                    return !$request->input('conversation_id');
                }),
                ]

        ]);

//        $user = Auth::user();
        $user =User::find(1);

        $conversation_id = $request->post('conversation_id');
        $user_id = $request->post('user_id');

        DB::beginTransaction();
        try {
            if ($conversation_id) {
                $conversation = $user->conversations()->findOrFail($conversation_id);
            } else {

                $conversation = Conversation::where('type', '=', 'peer')
                    ->whereHas('participants', function ($builder) use ($user_id, $user) {
                        $builder->join('participants as participants2', 'participants2.conversation_id', '=', 'participants.conversation_id')
                            ->where('participants.user_id', '=', $user_id)
                            ->where('participants2.user_id', '=', $user->id);
                    })->first();

                if (!$conversation) {
                    $conversation = Conversation::create([
                        'user_id' => $user->id,
                        'type' => 'peer',
                    ]);

                    $conversation->participants()->attach([
                        $user->id => ['joined_at' => now()],
                        $user_id => ['joined_at' => now()],
                    ]);
                }

            }

            $message = $conversation->messages()->create([
                'user_id'=>$user->id,
                'body'=>$request->post('message')
            ]);

            DB::statement('
             INSERT INTO  recipients (user_id,message_id)
             SELECT user_id ,? FROM participants
             where  conversation_id= ?
             ',
                [
                    $message->id,
                    $conversation->id
                ]);

            $conversation->update([
                'last_message_id'=>$message->id,
            ]);
            DB::commit();
            event(new MessageCreate($message));

        } catch (Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        return $message;
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id): array
    {
        Recipient::where([
            'user_id'=>Auth::user(),
            'message_id'=>$id
        ])->delete();
        return[
            'message'=>'deleted'
        ];
    }
}
