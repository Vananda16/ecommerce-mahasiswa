@extends('layouts.app')

@section('title', 'Produk Saya')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h3 class="fw-bold mb-0">Produk Saya</h3>
        <a href="{{ route('seller.products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Produk</a>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama produk…">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Semua status</option>
                @foreach(\App\Models\Product::STATUSES as $key => $meta)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $meta['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-outline-primary">Filter</button></div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Produk</th><th>Kategori</th><th class="text-end">Harga</th><th class="text-center">Stok</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" class="thumb" alt="">
                                    @else
                                        <span class="thumb-placeholder"><i class="bi bi-image"></i></span>
                                    @endif
                                    <div>
                                        <a href="{{ route('products.show', $product) }}" class="text-decoration-none fw-semibold">{{ $product->name }}</a>
                                        <div class="small text-muted">{{ $product->type_label }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->category?->name ?? '-' }}</td>
                            <td class="text-end">@rupiah($product->price)</td>
                            <td class="text-center">{{ $product->stock }}</td>
                            <td>
                                <span class="badge text-bg-{{ $product->status_color }}">{{ $product->status_label }}</span>
                                @if($product->rejection_reason)
                                    <div class="small text-danger">{{ $product->rejection_reason }}</div>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('seller.products.edit', $product) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('seller.products.destroy', $product) }}" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">Belum ada produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $products->links() }}</div>
@endsection
