@extends('layouts.app')

@section('title', 'Katalog Produk')

@section('content')
    <div class="p-4 p-md-5 mb-4 rounded-4 text-white" style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
        <h2 class="fw-bold mb-1">Cari kebutuhan kuliahmu di sini</h2>
        <p class="mb-3 opacity-75">Barang bekas, makanan, buku, hingga jasa dari sesama mahasiswa.</p>
        <form method="GET" action="{{ route('home') }}" class="row g-2">
            <div class="col-md-9">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-lg" placeholder="Cari produk berdasarkan nama…">
            </div>
            <div class="col-md-3 d-grid">
                <button class="btn btn-light btn-lg" type="submit"><i class="bi bi-search"></i> Cari</button>
            </div>
        </form>
    </div>

    <div class="row g-4">
        {{-- Filter --}}
        <div class="col-lg-3">
            <form method="GET" action="{{ route('home') }}" class="card">
                <div class="card-header">Filter</div>
                <div class="card-body">
                    <input type="hidden" name="q" value="{{ request('q') }}">

                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-select">
                            <option value="">Semua kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis</label>
                        <select name="type" class="form-select">
                            <option value="">Barang &amp; Jasa</option>
                            <option value="barang" @selected(request('type') === 'barang')>Barang</option>
                            <option value="jasa" @selected(request('type') === 'jasa')>Jasa</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rentang harga (Rp)</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text">Min</span>
                            <input type="number" min="0" name="min_price" value="{{ request('min_price') }}" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">Maks</span>
                            <input type="number" min="0" name="max_price" value="{{ request('max_price') }}" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Urutkan</label>
                        <select name="sort" class="form-select">
                            <option value="latest" @selected(request('sort', 'latest') === 'latest')>Terbaru</option>
                            <option value="price_asc" @selected(request('sort') === 'price_asc')>Harga terendah</option>
                            <option value="price_desc" @selected(request('sort') === 'price_desc')>Harga tertinggi</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" type="submit">Terapkan</button>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Daftar produk --}}
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Produk <span class="text-muted fw-normal">({{ $products->total() }})</span></h5>
            </div>

            @if($products->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5 text-muted">
                        <i class="bi bi-search fs-1"></i>
                        <p class="mb-0 mt-2">Tidak ada produk yang cocok dengan pencarianmu.</p>
                    </div>
                </div>
            @else
                <div class="row row-cols-2 row-cols-md-3 g-3">
                    @foreach($products as $product)
                        <div class="col">
                            @include('partials.product-card', ['product' => $product])
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
@endsection
