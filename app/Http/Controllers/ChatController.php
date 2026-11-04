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
                ->orderBy('id')
                ->get();
        }

        return view('admin.chat', compact('users', 'chats', 'selectedUser', 'totalMessages', 'unreadCount', 'totalCustomers', 'totalWorkers', 'unreadUserIds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if (empty($request->message) && !$request->hasFile('image')) {
            return back()->with('error', 'Pesan atau gambar tidak boleh kosong.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
        }

        // Pastikan admin_id valid dan ada di tabel users, atau ambil admin pertama, atau fallback null
        $adminId = null;
        if ($request->filled('admin_id') && User::where('id', $request->admin_id)->exists()) {
            $adminId = (int) $request->admin_id;
        } elseif ($defaultAdmin = User::where('role', 'admin')->first()) {
            $adminId = $defaultAdmin->id;
        }

        Chat::create([
            'user_id' => auth()->id(),
            'admin_id' => $adminId,
            'sender' => 'user',
            'message' => $request->message,
            'image' => $imagePath,
        ]);
        return redirect()->route('chatuser');
    }

    public function chatuser()
    {
        $chats = Chat::where('user_id', auth()->id())->orderBy('id')->get();
        return view('user.chatuser', compact('chats'));
    }

    public function messagesForAdmin(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'after_id' => 'nullable|integer|min:0',
        ]);

        return $this->newMessagesResponse($validated['user_id'], $validated['after_id'] ?? 0);
    }

    public function messagesForUser(Request $request)
    {
        $validated = $request->validate([
            'after_id' => 'nullable|integer|min:0',
        ]);

        return $this->newMessagesResponse(auth()->id(), $validated['after_id'] ?? 0);
    }

    private function newMessagesResponse(int $userId, int $afterId)
    {
        $messages = Chat::where('user_id', $userId)
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get(['id', 'sender', 'message', 'image', 'created_at'])
            ->map(function (Chat $chat) {
                return [
                    'id' => $chat->id,
                    'sender' => $chat->sender,
                    'message' => $chat->message,
                    'image_url' => $chat->image ? asset('storage/' . $chat->image) : null,
                    'time' => $chat->created_at->copy()->timezone('Asia/Jakarta')->format('H:i'),
                ];
            });

        return response()->json(['messages' => $messages])->header('Cache-Control', 'no-store');
    }

    public function send(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if (empty($request->message) && !$request->hasFile('image')) {
            return back()->with('error', 'Pesan atau gambar tidak boleh kosong.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
        }

        Chat::create([
            'user_id' => $request->user_id,           // user tujuan
            'admin_id' => auth()->id(),               // admin sebagai pengirim
            'sender' => 'admin',                      // penanda pesan dari admin
            'message' => $request->message,
            'image' => $imagePath,
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
