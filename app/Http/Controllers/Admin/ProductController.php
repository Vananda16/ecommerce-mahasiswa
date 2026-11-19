<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['store.user', 'category'])
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->input('q').'%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->input('category')))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function approve(Product $product): RedirectResponse
    {
        $product->update(['status' => Product::APPROVED, 'rejection_reason' => null]);

        return back()->with('success', "Produk \"{$product->name}\" disetujui dan tampil di katalog.");
    }

    public function reject(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:255'],
        ]);

        $product->update([
            'status' => Product::REJECTED,
            'rejection_reason' => $data['rejection_reason'],
        ]);

        return back()->with('success', "Produk \"{$product->name}\" ditolak.");
    }

    /** Hapus produk yang melanggar aturan. */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $name = $product->name;
        $product->delete();

        return back()->with('success', "Produk \"{$name}\" dihapus.");
    }
}
