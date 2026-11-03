<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chat;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua user yang pernah chat
        $users = User::whereHas('chats')->get();

        // Hitung statistik
        $totalMessages = Chat::count();
        $totalCustomers = User::whereHas('chats')->count();
        $totalWorkers = User::where('role', 'worker')->whereHas('chats')->count();

        // Tentukan thread yang belum dibaca (definisi: pesan terakhir dari user)
        $latestPerUser = Chat::orderBy('created_at')->get()->groupBy('user_id')->map(function ($items) {
            return $items->last();
        });
        $unreadUserIds = $latestPerUser->filter(function ($chat) {
            return $chat && $chat->sender === 'user';
        })->keys()->toArray();
        $unreadCount = count($unreadUserIds);

        // User yang dipilih (jika ada)
        $selectedUser = null;
        $chats = collect();

        if ($request->has('user_id')) {
            $selectedUser = User::find($request->user_id);
            $chats = Chat::with('user')
                ->where('user_id', $request->user_id)
                ->orderBy('created_at')
                ->get();
        }

        return view('admin.chat', compact('users', 'chats', 'selectedUser', 'totalMessages', 'unreadCount', 'totalCustomers', 'totalWorkers', 'unreadUserIds'));
    }

    public function store(Request $request)
    {
        \App\Models\Chat::create([
            'user_id' => auth()->id(),
            'sender' => 'user',
            'message' => $request->message,
        ]);
        return redirect()->route('chatuser');
    }

    public function chatuser()
    {
        $chats = Chat::where('user_id', auth()->id())->get(); // atau sesuaikan query-nya
        return view('user.chatuser', compact('chats'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        Chat::create([
            'user_id' => $request->user_id,           // user tujuan
            'admin_id' => auth()->id(),               // admin sebagai pengirim
            'sender' => 'admin',                      // penanda pesan dari admin
            'message' => $request->message,
        ]);

        return redirect()->route('chat', ['user_id' => $request->user_id]);
    }

    public function sendadmin(Request $request)
    {
        \App\Models\Chat::create([
            'admin_id' => $request->user_id,
            'sender' => 'user',
            'message' => $request->message,
        ]);
        return redirect()->route('chat');
    }

    public function storeadmin(Request $request)
    {
        $request->validate([
            'admin_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        Chat::create([
            'user_id' => auth()->id(),                // user sebagai pengirim
            'admin_id' => $request->admin_id,         // admin tujuan
            'sender' => 'user',                       // penanda pesan dari user
            'message' => $request->message,
        ]);

        return redirect()->route('chatuser', ['admin_id' => $request->admin_id]);
    }
}
