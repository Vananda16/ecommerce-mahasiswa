<?php

namespace App\Http\Controllers\Seller;

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
        $products = $request->user()->store->products()
            ->with('category')
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->input('q').'%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('seller.products.index', ['products' => $products]);
    }

    public function create(): View
    {
        return view('seller.products.form', [
            'product' => new Product(['type' => 'barang', 'stock' => 1]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        // Produk baru harus diverifikasi admin dulu sebelum tampil di katalog.
        $data['status'] = Product::PENDING;

        $request->user()->store->products()->create($data);

        return redirect()->route('seller.products.index')
            ->with('success', 'Produk ditambahkan. Menunggu verifikasi admin sebelum tampil di katalog.');
    }

    public function edit(Request $request, Product $product): View
    {
        $this->authorizeProduct($request, $product);

        return view('seller.products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->authorizeProduct($request, $product);

        $data = $request->validate($this->rules());

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        } else {
            unset($data['image']);
        }

        // Produk yang ditolak dan diperbaiki diajukan ulang untuk diverifikasi.
        if ($product->status === Product::REJECTED) {
            $data['status'] = Product::PENDING;
            $data['rejection_reason'] = null;
        }

        $product->update($data);

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $this->authorizeProduct($request, $product);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('seller.products.index')->with('success', 'Produk dihapus.');
    }

    private function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'type' => ['required', 'in:barang,jasa'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:3000'],
            'price' => ['required', 'integer', 'min:0', 'max:999999999'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    private function authorizeProduct(Request $request, Product $product): void
    {
        abort_unless($product->store_id === $request->user()->store->id, 403);
    }
}
