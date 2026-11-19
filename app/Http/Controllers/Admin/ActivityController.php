<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Order;
use App\Models\Product;
use App\Models\Report;
use App\Models\Review;
use App\Models\User;
use Illuminate\View\View;

/**
 * Pemantauan aktivitas jual beli: gabungan kejadian terbaru dalam satu linimasa.
 */
class ActivityController extends Controller
{
    public function index(): View
    {
        $events = collect();

        foreach (Order::with(['buyer', 'store'])->latest()->take(15)->get() as $order) {
            $events->push([
                'time' => $order->created_at,
                'icon' => 'bag-check',
                'color' => 'primary',
                'text' => "{$order->buyer->name} memesan di {$order->store->name} ({$order->code}) — status: {$order->status_label}",
                'url' => route('admin.transactions.show', $order),
            ]);
        }

        foreach (Product::with('store')->latest()->take(10)->get() as $product) {
            $events->push([
                'time' => $product->created_at,
                'icon' => 'box-seam',
                'color' => 'warning',
                'text' => "{$product->store->name} mengunggah produk \"{$product->name}\" ({$product->status_label})",
                'url' => route('admin.products.index', ['q' => $product->name]),
            ]);
        }

        foreach (User::where('role', '!=', User::ADMIN)->latest()->take(10)->get() as $user) {
            $events->push([
                'time' => $user->created_at,
                'icon' => 'person-plus',
                'color' => 'success',
                'text' => "{$user->name} mendaftar sebagai {$user->role_label}",
                'url' => route('admin.users.index', ['q' => $user->email]),
            ]);
        }

        foreach (Review::with(['buyer', 'store'])->latest()->take(8)->get() as $review) {
            $events->push([
                'time' => $review->created_at,
                'icon' => 'star',
                'color' => 'info',
                'text' => "{$review->buyer->name} memberi {$review->rating}/5 untuk {$review->store->name}",
                'url' => null,
            ]);
        }

        foreach (Report::with('reporter')->latest()->take(8)->get() as $report) {
            $events->push([
                'time' => $report->created_at,
                'icon' => 'flag',
                'color' => 'danger',
                'text' => "{$report->reporter->name} mengirim pengaduan: {$report->subject}",
                'url' => route('admin.complaints.show', $report),
            ]);
        }

        return view('admin.activity.index', [
            'events' => $events->sortByDesc('time')->take(40)->values(),
            'today' => [
                'orders' => Order::whereDate('created_at', today())->count(),
                'users' => User::where('role', '!=', User::ADMIN)->whereDate('created_at', today())->count(),
                'products' => Product::whereDate('created_at', today())->count(),
                'messages' => Message::whereDate('created_at', today())->count(),
            ],
        ]);
    }
}
