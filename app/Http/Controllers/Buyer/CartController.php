<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $items = CartItem::with('product.store')
            ->where('user_id', $request->user()->id)
            ->get();

        return view('buyer.cart.index', [
            'groups' => $items->groupBy(fn (CartItem $item) => $item->product->store_id),
            'total' => $items->sum(fn (CartItem $item) => $item->subtotal),
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless(Product::visible()->whereKey($product->id)->exists(), 404);

        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $item = CartItem::firstOrNew([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
        ]);

        $newQty = ($item->exists ? $item->quantity : 0) + ($data['quantity'] ?? 1);

        if ($newQty > $product->stock) {
            return back()->with('error', "Stok \"{$product->name}\" hanya tersisa {$product->stock}.");
        }

        $item->quantity = $newQty;
        $item->save();

        return redirect()->route('cart.index')->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, CartItem $item): RedirectResponse
    {
        abort_unless($item->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        if ($data['quantity'] > $item->product->stock) {
            return back()->with('error', "Stok \"{$item->product->name}\" hanya tersisa {$item->product->stock}.");
        }

        $item->update(['quantity' => $data['quantity']]);

        return back()->with('success', 'Jumlah produk diperbarui.');
    }

    public function destroy(Request $request, CartItem $item): RedirectResponse
    {
        abort_unless($item->user_id === $request->user()->id, 403);

        $item->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
