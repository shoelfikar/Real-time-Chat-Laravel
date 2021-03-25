<?php

namespace App\Http\Controllers;

use App\Message;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;

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
        // $users = User::where('id', '!=', Auth::user()->id)->orderBy('id', 'desc')->get();

        $users = DB::select("SELECT users.id, users.name, users.email, count(is_read) AS unread FROM users LEFT JOIN messages ON users.id = messages.from AND is_read = 0 AND messages.to = " . Auth::user()->id ." WHERE users.id != ".   Auth::user()->id . " GROUP BY users.id, users.name, users.email");

        return view('home', ['users'=> $users]);
    }


    public function getMessage($user_id)
    {
        $my_id = Auth::user()->id;
        Message::where(['from'=> $user_id, 'to'=> $my_id])->update(['is_read'=> 1]);
        $messages = Message::where(function($query) use ($user_id, $my_id){
            $query->where('from', $my_id)->where('to', $user_id);
        })->orWhere(function($query) use ($user_id, $my_id){
            $query->where('from', $user_id)->where('to', $my_id);
        })->get();

        return view('messages', ['messages'=> $messages]);
    }


    public function sendMessage(Request $request)
    {
        $my_id = Auth::user()->id;
        $received_id = $request->received_id;
        $message = $request->message;

        $messages = new Message();
        $messages->from = $my_id;
        $messages->to = $received_id;
        $messages->message = $message;
        $messages->is_read = 0;
        $messages->save();


        $option =  [
            'cluster' => 'ap1',
            'useTLS' => true
        ];

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $option
        );

        $data = ['from'=> $my_id, 'to'=> $received_id ];
        $pusher->trigger('my-channel', 'my-event', $data);
    }
}
