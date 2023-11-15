<?php

namespace App\Http\Controllers;

use App\Events\PrivateSendMessage;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Auth;

use App\Events\SendMessage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
        return view('chat');
    }

    public function chat()
    {
        return view('chat');
    }

    public function private()
    {
        return view('private');
    }

    public function users()
    {
        return User::all();
    }

    public function messages(){
        return Message::with('user')->get();
    }

    public function messageStore(Request $request) {
        $user = Auth::user();

        $message = $user->messages()
                        ->create([
                            'message' => $request->message,
                        ]);

        broadcast(new SendMessage($user, $message))->toOthers();

        return 'message sent';
    }

    public function privateMessageStore(Request $request)
        {
            $user = Auth::user();

            $request->validate([
                'receiver_id' => '', // Assuming receiver_id corresponds to the 'id' column in the 'users' table.
                'message' => 'required|string',
            ]);

            $receiverId = $request->input('receiver_id');

            // Assuming you have a relationship between User model and Message model.
            $message = $user->messages()->create([
                'receiver_id' => $receiverId,
                'message' => $request->input('message'),
            ]);

            broadcast(new SendMessage($user, $message))->toOthers();

            return response()->json(['status' => 'success', 'message' => 'Message sent']);
        }


//    public function sendPrivateMessage( Request $request){
//        $user = Auth::user();
//        $messages = $user->messages()->create($request->all());
//
//        broadcast(new SendMessage($user, $messages))->toOthers();
//
//        return 'private message sent';
//    }

//    public function privateMessages(User $user, Request $request)
//    {
//        $input = $request->all();
//        $input['reciver_id'] = $user->id;
//        $privateCommunication= Message::with('user')
//                                      ->where(['user_id'=> auth()->id(), 'receiver_id'=> $user->id])
//                                      ->orWhere(function($query) use($user){
//                                          $query->where(['user_id' => $user->id, 'receiver_id' => auth()->id()]);
//                                      })
//                                      ->get();
//
//        return $privateCommunication;
//    }


    public function privateMessages(User $user)
    {
        $privateCommunication = Message::with('user')
            ->where(['user_id'=> auth()->id(), 'receiver_id'=> $user->id])
            ->orWhere(function($query) use($user){
                $query->where(['user_id' => $user->id, 'receiver_id'=>auth()->id()]);
            })
            ->get();

        return $privateCommunication;
    }

    public function sendPrivateMessage(Request $request, User $user)
    {
        $input = $request->all();
        $input['receiver_id'] = $user->id;

        // Assuming your Message model has the necessary fillable fields, like 'user_id', 'receiver_id', 'message'
        $message = auth()->user()->messages()->create($input);

        broadcast(new PrivateSendMessage($message->load('user')))->toOthers();

        return response(['status' => 'Private message sent', 'message' => $message]);
    }
}
