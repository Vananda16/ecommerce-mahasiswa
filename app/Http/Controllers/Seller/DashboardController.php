<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $store = $request->user()->store;

        return view('seller.dashboard', [
            'store' => $store,
            'stats' => [
                'products' => $store->products()->count(),
                'pending_products' => $store->products()->where('status', Product::PENDING)->count(),
                'new_orders' => $store->orders()->where('status', Order::PENDING)->count(),
                'in_progress' => $store->orders()->whereIn('status', [Order::ACCEPTED, Order::PROCESSING, Order::SHIPPED])->count(),
                'revenue' => (int) $store->orders()->where('status', Order::COMPLETED)->sum('total'),
                'rating' => $store->average_rating,
                'reviews' => $store->reviews()->count(),
            ],
            'recentOrders' => $store->orders()->with(['buyer', 'payment'])->latest()->take(6)->get(),
            'lowStock' => $store->products()->where('status', Product::APPROVED)->where('stock', '<=', 5)->orderBy('stock')->take(5)->get(),
        ]);
    }
}
