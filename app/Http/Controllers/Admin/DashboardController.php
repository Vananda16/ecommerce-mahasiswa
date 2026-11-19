<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Report;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Data grafik 6 bulan terakhir (jumlah pesanan & omzet pesanan selesai)
        $start = now()->subMonths(5)->startOfMonth();

        $orders = Order::where('created_at', '>=', $start)->get(['status', 'total', 'created_at']);

        $labels = [];
        $orderCounts = [];
        $revenues = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->startOfMonth();
            $key = $month->format('Y-m');

            $inMonth = $orders->filter(fn ($o) => $o->created_at->format('Y-m') === $key);

            $labels[] = $month->translatedFormat('M Y');
            $orderCounts[] = $inMonth->count();
            $revenues[] = (int) $inMonth->where('status', Order::COMPLETED)->sum('total');
        }

        return view('admin.dashboard', [
            'stats' => [
                'sellers' => User::where('role', User::SELLER)->count(),
                'buyers' => User::where('role', User::BUYER)->count(),
                'blocked' => User::where('is_blocked', true)->count(),
                'products' => Product::count(),
                'pending_products' => Product::where('status', Product::PENDING)->count(),
                'orders' => Order::count(),
                'orders_today' => Order::whereDate('created_at', today())->count(),
                'revenue' => (int) Order::where('status', Order::COMPLETED)->sum('total'),
                'open_reports' => Report::where('status', Report::OPEN)->count(),
            ],
            'chart' => [
                'labels' => $labels,
                'orders' => $orderCounts,
                'revenue' => $revenues,
            ],
            'pendingProducts' => Product::with('store')->where('status', Product::PENDING)->latest()->take(5)->get(),
            'recentOrders' => Order::with(['buyer', 'store'])->latest()->take(5)->get(),
        ]);
    }
}
