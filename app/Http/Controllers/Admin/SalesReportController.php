<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * Laporan penjualan (per toko) dan pembelian (per pembeli) untuk pesanan yang selesai.
 */
class SalesReportController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();

        $orders = Order::where('status', Order::COMPLETED)
            ->whereBetween('created_at', [$from, $to])
            ->with(['store', 'buyer', 'items'])
            ->latest()
            ->get();

        $bySeller = $orders->groupBy('store_id')
            ->map(fn ($group) => [
                'name' => $group->first()->store->name,
                'orders' => $group->count(),
                'items' => $group->sum(fn ($o) => $o->items->sum('quantity')),
                'total' => $group->sum('total'),
            ])
            ->sortByDesc('total')
            ->values();

        $byBuyer = $orders->groupBy('buyer_id')
            ->map(fn ($group) => [
                'name' => $group->first()->buyer->name,
                'nim' => $group->first()->buyer->nim,
                'orders' => $group->count(),
                'items' => $group->sum(fn ($o) => $o->items->sum('quantity')),
                'total' => $group->sum('total'),
            ])
            ->sortByDesc('total')
            ->values();

        return view('admin.sales-report.index', [
            'from' => $from,
            'to' => $to,
            'orders' => $orders,
            'bySeller' => $bySeller,
            'byBuyer' => $byBuyer,
            'grandTotal' => $orders->sum('total'),
        ]);
    }
}
