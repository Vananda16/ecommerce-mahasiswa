<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $orders = Order::query()
            ->with(['buyer', 'store', 'payment'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $s = $request->input('q');
                $query->where(fn ($w) => $w->where('code', 'like', "%{$s}%")
                    ->orWhereHas('buyer', fn ($b) => $b->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('store', fn ($b) => $b->where('name', 'like', "%{$s}%")));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('payment'), fn ($q) => $q->whereHas('payment', fn ($p) => $p->where('status', $request->input('payment'))))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('to')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.transactions.index', ['orders' => $orders]);
    }

    public function show(Order $transaction): View
    {
        $transaction->load(['items.product', 'buyer', 'store.user', 'payment', 'review']);

        return view('admin.transactions.show', ['order' => $transaction]);
    }

    /** Admin dapat mengoreksi status pesanan / pembayaran bila terjadi sengketa. */
    public function update(Request $request, Order $transaction): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
            'payment_status' => ['required', Rule::in(array_keys(Payment::STATUSES))],
        ]);

        $wasClosed = $transaction->isClosed();
        $willClose = in_array($data['status'], [Order::REJECTED, Order::CANCELLED], true);

        if ($wasClosed && $data['status'] !== $transaction->status) {
            return back()->with('error', 'Pesanan yang sudah ditolak/dibatalkan tidak dapat diaktifkan kembali.');
        }

        DB::transaction(function () use ($transaction, $data, $wasClosed, $willClose) {
            if (! $wasClosed && $willClose) {
                $transaction->restock();
            }

            $transaction->update([
                'status' => $data['status'],
                'completed_at' => $data['status'] === Order::COMPLETED
                    ? ($transaction->completed_at ?? now())
                    : $transaction->completed_at,
            ]);

            $payment = $transaction->payment;
            $payment->status = $data['payment_status'];
            if ($data['payment_status'] === Payment::PAID && ! $payment->paid_at) {
                $payment->paid_at = now();
            }
            $payment->save();
        });

        return back()->with('success', 'Transaksi diperbarui.');
    }
}
