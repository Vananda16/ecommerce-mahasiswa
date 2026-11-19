<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()->store->orders()
            ->with(['buyer', 'payment', 'items'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('seller.orders.index', ['orders' => $orders]);
    }

    public function show(Request $request, Order $order): View
    {
        $this->authorizeOrder($request, $order);

        $order->load(['items.product', 'buyer', 'payment', 'review']);

        return view('seller.orders.show', ['order' => $order]);
    }

    public function accept(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($request, $order);

        if ($order->status !== Order::PENDING) {
            return back()->with('error', 'Pesanan ini sudah diproses sebelumnya.');
        }

        $order->update(['status' => Order::ACCEPTED]);

        return back()->with('success', 'Pesanan diterima.');
    }

    public function reject(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($request, $order);

        if ($order->status !== Order::PENDING) {
            return back()->with('error', 'Hanya pesanan yang menunggu konfirmasi yang bisa ditolak.');
        }

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($order, $data) {
            $order->restock();
            $order->update([
                'status' => Order::REJECTED,
                'rejection_reason' => $data['rejection_reason'],
            ]);
        });

        return back()->with('success', 'Pesanan ditolak dan stok dikembalikan.');
    }

    /** Lanjutkan status: diterima -> diproses -> dikirim -> selesai. */
    public function advance(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($request, $order);

        $next = $order->next_status;

        if (! $next) {
            return back()->with('error', 'Status pesanan tidak dapat dilanjutkan.');
        }

        $order->loadMissing('payment');

        // Untuk transfer / e-wallet, pembayaran harus lunas dulu sebelum barang dikirim.
        if ($next === Order::SHIPPED && ! $order->payment->isCod() && $order->payment->status !== Payment::PAID) {
            return back()->with('error', 'Konfirmasi pembayaran pembeli terlebih dahulu sebelum mengirim pesanan.');
        }

        DB::transaction(function () use ($order, $next) {
            $attributes = ['status' => $next];

            if ($next === Order::COMPLETED) {
                $attributes['completed_at'] = now();

                // COD dianggap lunas saat pesanan selesai.
                if ($order->payment->isCod() && $order->payment->status !== Payment::PAID) {
                    $order->payment->update(['status' => Payment::PAID, 'paid_at' => now()]);
                }
            }

            $order->update($attributes);
        });

        return back()->with('success', 'Status pesanan diperbarui menjadi "'.Order::STATUSES[$next]['label'].'".');
    }

    public function confirmPayment(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($request, $order);

        if ($order->payment->status !== Payment::WAITING) {
            return back()->with('error', 'Tidak ada bukti pembayaran yang menunggu verifikasi.');
        }

        $order->payment->update(['status' => Payment::PAID, 'paid_at' => now()]);

        return back()->with('success', 'Pembayaran dikonfirmasi lunas.');
    }

    public function rejectPayment(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($request, $order);

        if ($order->payment->status !== Payment::WAITING) {
            return back()->with('error', 'Tidak ada bukti pembayaran yang menunggu verifikasi.');
        }

        $data = $request->validate([
            'note' => ['required', 'string', 'max:255'],
        ]);

        $order->payment->update(['status' => Payment::REJECTED, 'note' => $data['note']]);

        return back()->with('success', 'Bukti pembayaran ditolak. Pembeli diminta mengunggah ulang.');
    }

    private function authorizeOrder(Request $request, Order $order): void
    {
        abort_unless($order->store_id === $request->user()->store->id, 403);
    }
}
