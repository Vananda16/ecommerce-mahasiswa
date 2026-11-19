<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()->orders()
            ->with(['store', 'payment', 'items'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('buyer.orders.index', ['orders' => $orders]);
    }

    /** Checkout: satu pesanan dibuat per toko. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'shipping_address' => ['required', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', Rule::in(array_keys(Payment::METHODS))],
        ]);

        $user = $request->user();
        $items = CartItem::with('product')->where('user_id', $user->id)->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        $orders = DB::transaction(function () use ($items, $data, $user) {
            $created = collect();

            $products = Product::with('store.user')
                ->whereIn('id', $items->pluck('product_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($items->groupBy(fn (CartItem $i) => $products[$i->product_id]->store_id) as $storeId => $group) {
                $total = 0;
                $lines = [];

                foreach ($group as $item) {
                    $product = $products[$item->product_id];

                    if (
                        $product->status !== Product::APPROVED
                        || $product->store->user->is_blocked
                        || $product->stock < $item->quantity
                    ) {
                        throw ValidationException::withMessages([
                            'cart' => "Produk \"{$product->name}\" tidak tersedia atau stok tidak mencukupi.",
                        ]);
                    }

                    $subtotal = $product->price * $item->quantity;
                    $total += $subtotal;

                    $lines[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $item->quantity,
                        'subtotal' => $subtotal,
                    ];

                    $product->decrement('stock', $item->quantity);
                }

                $order = Order::create([
                    'code' => Order::generateCode(),
                    'buyer_id' => $user->id,
                    'store_id' => $storeId,
                    'total' => $total,
                    'status' => Order::PENDING,
                    'shipping_address' => $data['shipping_address'],
                    'note' => $data['note'] ?? null,
                ]);

                $order->items()->createMany($lines);
                $order->payment()->create([
                    'method' => $data['payment_method'],
                    'status' => Payment::UNPAID,
                ]);

                $created->push($order);
            }

            CartItem::where('user_id', $user->id)->delete();

            return $created;
        });

        if ($orders->count() === 1) {
            return redirect()->route('orders.show', $orders->first())
                ->with('success', 'Pesanan berhasil dibuat. Silakan lakukan pembayaran.');
        }

        return redirect()->route('orders.index')
            ->with('success', $orders->count().' pesanan berhasil dibuat (satu per toko).');
    }

    public function show(Request $request, Order $order): View
    {
        $this->authorizeOrder($request, $order);

        $order->load(['items.product', 'store.user', 'payment', 'review']);

        return view('buyer.orders.show', ['order' => $order]);
    }

    /** Unggah bukti pembayaran (konfirmasi pembayaran). */
    public function pay(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($request, $order);

        $payment = $order->payment;

        abort_if($payment->isCod(), 404);

        if ($order->isClosed() || $payment->status === Payment::PAID) {
            return back()->with('error', 'Pembayaran untuk pesanan ini tidak dapat diubah.');
        }

        $data = $request->validate([
            'proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        if ($payment->proof) {
            Storage::disk('public')->delete($payment->proof);
        }

        $payment->update([
            'proof' => $request->file('proof')->store('payments', 'public'),
            'status' => Payment::WAITING,
            'note' => $data['note'] ?? null,
        ]);

        return back()->with('success', 'Bukti pembayaran terkirim. Menunggu verifikasi penjual.');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($request, $order);

        if ($order->status !== Order::PENDING) {
            return back()->with('error', 'Pesanan hanya bisa dibatalkan selama belum diterima penjual.');
        }

        DB::transaction(function () use ($order) {
            $order->restock();
            $order->update(['status' => Order::CANCELLED]);
        });

        return back()->with('success', 'Pesanan dibatalkan.');
    }

    private function authorizeOrder(Request $request, Order $order): void
    {
        abort_unless($order->buyer_id === $request->user()->id, 403);
    }
}
