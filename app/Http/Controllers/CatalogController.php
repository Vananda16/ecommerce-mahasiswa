<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer'],
            'type' => ['nullable', 'in:barang,jasa'],
            'min_price' => ['nullable', 'integer', 'min:0'],
            'max_price' => ['nullable', 'integer', 'min:0'],
            'sort' => ['nullable', 'in:latest,price_asc,price_desc'],
        ]);

        $products = Product::query()
            ->visible()
            ->with(['store', 'category'])
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->input('q').'%'))
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->input('category')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->input('type')))
            ->when($request->filled('min_price'), fn ($q) => $q->where('price', '>=', (int) $request->input('min_price')))
            ->when($request->filled('max_price'), fn ($q) => $q->where('price', '<=', (int) $request->input('max_price')))
            ->when(
                $request->input('sort'),
                fn ($q, $sort) => match ($sort) {
                    'price_asc' => $q->orderBy('price'),
                    'price_desc' => $q->orderByDesc('price'),
                    default => $q->latest(),
                },
                fn ($q) => $q->latest()
            )
            ->paginate(12)
            ->withQueryString();

        return view('catalog.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function show(Request $request, Product $product): View
    {
        $user = $request->user();

        $visible = Product::visible()->whereKey($product->id)->exists();
        $isOwner = $user && $user->isSeller() && $user->store && $user->store->id === $product->store_id;

        // Produk yang belum disetujui hanya boleh dilihat pemilik & admin.
        abort_unless($visible || $isOwner || ($user && $user->isAdmin()), 404);

        $product->load(['store.user', 'category']);

        return view('catalog.show', [
            'product' => $product,
            'reviews' => $product->store->reviews()->with('buyer')->latest()->take(5)->get(),
            'related' => Product::visible()
                ->where('id', '!=', $product->id)
                ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
                ->latest()->take(4)->get(),
        ]);
    }
}
