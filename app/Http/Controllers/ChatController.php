<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $me = $request->user();

        $partnerIds = Message::where('sender_id', $me->id)->pluck('receiver_id')
            ->merge(Message::where('receiver_id', $me->id)->pluck('sender_id'))
            ->unique()
            ->values();

        $conversations = User::with('store')
            ->whereIn('id', $partnerIds)
            ->get()
            ->map(fn (User $partner) => [
                'user' => $partner,
                'last' => Message::between($me->id, $partner->id)->latest('id')->first(),
                'unread' => Message::where('sender_id', $partner->id)
                    ->where('receiver_id', $me->id)
                    ->whereNull('read_at')
                    ->count(),
            ])
            ->sortByDesc(fn (array $c) => $c['last']?->id)
            ->values();

        return view('chat.index', ['conversations' => $conversations]);
    }

    public function show(Request $request, User $user): View
    {
        $me = $request->user();
        $this->authorizeChat($me, $user);

        // Tandai pesan masuk dari lawan bicara sebagai sudah dibaca
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $me->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::between($me->id, $user->id)
            ->latest('id')
            ->take(200)
            ->get()
            ->reverse()
            ->values();

        return view('chat.show', [
            'partner' => $user->load('store'),
            'messages' => $messages,
        ]);
    }

    public function store(Request $request, User $user): RedirectResponse
    {
        $me = $request->user();
        $this->authorizeChat($me, $user);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        Message::create([
            'sender_id' => $me->id,
            'receiver_id' => $user->id,
            'body' => $data['body'],
        ]);

        return redirect()->route('chat.show', $user);
    }

    /** Chat hanya boleh antara satu penjual dan satu pembeli. */
    private function authorizeChat(User $me, User $other): void
    {
        $roles = [User::SELLER, User::BUYER];

        abort_unless(
            $me->id !== $other->id
            && in_array($me->role, $roles, true)
            && in_array($other->role, $roles, true)
            && $me->role !== $other->role,
            403,
            'Chat hanya tersedia antara penjual dan pembeli.'
        );
    }
}
