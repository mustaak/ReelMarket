<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        return view('orders.index', [
            'orders' => $user->orders()
                ->withCount('items')
                ->latest()
                ->paginate(12),
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        $user = $request->user();
        abort_unless($user instanceof User && $order->user_id === $user->id, 404);

        return view('orders.show', [
            'order' => $order->load('items'),
        ]);
    }
}
