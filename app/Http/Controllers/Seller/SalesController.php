<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * Riwayat penjualan (pesanan berstatus selesai).
 */
class SalesController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();

        $query = $request->user()->store->orders()
            ->where('status', Order::COMPLETED)
            ->whereBetween('created_at', [$from, $to]);

        return view('seller.sales.index', [
            'orders' => (clone $query)->with(['buyer', 'items'])->latest()->paginate(15)->withQueryString(),
            'totalRevenue' => (int) (clone $query)->sum('total'),
            'totalOrders' => (clone $query)->count(),
            'from' => $from,
            'to' => $to,
        ]);
    }
}
