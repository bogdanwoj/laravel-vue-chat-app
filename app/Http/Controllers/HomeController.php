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
                'receiver_id' => '',
                'message' => 'required|string',
            ]);

            $receiverId = $request->input('receiver_id');

            $message = $user->messages()->create([
                'receiver_id' => $receiverId,
                'message' => $request->input('message'),
            ]);

            broadcast(new PrivateSendMessage($message))->toOthers();

            return response()->json(['status' => 'success', 'message' => 'Message sent']);
        }



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

            $message = auth()->user()->messages()->create($input);

            broadcast(new PrivateSendMessage($message->load('user')))->toOthers();

            return response(['status' => 'Private message sent', 'message' => $message]);
        }
    }
