<?php
namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ChatController extends Controller
{
    public function show(Order $order, User $seller)
    {
        // Must belong to order as buyer or be the seller
        if (Auth::id() !== $order->user_id && Auth::id() !== $seller->id) {
            abort(403);
        }

        $conversation = Conversation::firstOrCreate([
            'order_id'  => $order->id,
            'buyer_id'  => $order->user_id,
            'seller_id' => $seller->id,
        ]);

        $messages = $conversation->messages()->with('sender')->oldest()->get();

        return view('chat.show', compact('conversation', 'messages', 'order', 'seller'));
    }

    public function store(Request $request, Conversation $conversation)
    {
        if (Auth::id() !== $conversation->buyer_id && Auth::id() !== $conversation->seller_id) {
            abort(403);
        }

        $request->validate(['body' => 'required|string|max:1000']);

        $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'body'      => $request->body,
        ]);

        return back();
    }
}
