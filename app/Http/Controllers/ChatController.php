<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    //
    public function index()
    {
        return Inertia::render('Chat/Index', [
            'messages' => Message::with('user')->latest()->take(30)->get(),
            'user' =>  Auth::user()->id,
        ]);
    }   
    public function store(Request $request)
    {
        $request->validate(['body' => 'required|string']);

        $msg = Message::create([
            'user_id' => Auth::user()->id,
            'body'    => $request->body,
        ]);

        broadcast(new MessageSent($msg->load('user')))->toOthers();

        return response()->json([
            'message' => $msg,
            'status' => 'Message Sent!',
        ]);
    }
    public function send(Request $request)
    {
        $message = [
            'user' => Auth::user()->name,
            'text' => $request->input('text'),
            'time' => now()->toDateTimeString(),
        ];

        //broadcast(new MessageSent(...))->toOthers();

        return response()->json(['status' => 'Message Sent!']);
    }

}