@extends('layouts.app')

@section('title', $product->name)

@section('content')
    @php
        $me = auth()->user();
        $isOwner = $me && $me->isSeller() && $me->store && $me->store->id === $product->store_id;
        $sellerUser = $product->store->user;
    @endphp

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Katalog</a></li>
            @if($product->category)
                <li class="breadcrumb-item"><a href="{{ route('home', ['category' => $product->category_id]) }}">{{ $product->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active">{{ \Illuminate\Support\Str::limit($product->name, 40) }}</li>
        </ol>
    </nav>

    @if($product->status !== 'approved')
        <div class="alert alert-warning">
            Produk ini belum tampil di katalog publik:
            <strong>{{ $product->status_label }}</strong>
            @if($product->rejection_reason) — {{ $product->rejection_reason }} @endif
        </div>
    @endif

    <div class="row g-4">
        <div class="col-md-5">
            <div class="card">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" class="img-fluid rounded-3" alt="{{ $product->name }}">
                @else
                    <div class="product-img-placeholder" style="border-radius:.75rem"><i class="bi bi-image"></i></div>
                @endif
            </div>
        </div>

        <div class="col-md-7">
            <div class="mb-2">
                <span class="badge text-bg-light border">{{ $product->type_label }}</span>
                @if($product->category)<span class="badge text-bg-light border">{{ $product->category->name }}</span>@endif
            </div>
            <h2 class="fw-bold">{{ $product->name }}</h2>
            <h3 class="text-primary fw-bold mb-3">@rupiah($product->price)</h3>

            <p class="mb-1">
                <i class="bi bi-boxes"></i>
                {{ $product->type === 'jasa' ? 'Kuota tersedia' : 'Stok' }}:
                @if($product->stock > 0)
                    <strong>{{ $product->stock }}</strong>
                @else
                    <span class="badge bg-secondary">Habis</span>
                @endif
            </p>
            <p class="mb-3">
                <i class="bi bi-shop"></i> <strong>{{ $product->store->name }}</strong>
                <span class="ms-2">@include('partials.stars', ['value' => $product->store->average_rating])</span>
                <span class="text-muted small">({{ number_format($product->store->average_rating, 1) }})</span>
            </p>

            <div class="mb-4" style="white-space:pre-line">{{ $product->description }}</div>

            {{-- Aksi sesuai role --}}
            @guest
                <div class="alert alert-light border">
                    <a href="{{ route('login') }}">Masuk</a> atau <a href="{{ route('register') }}">daftar</a> sebagai pembeli untuk memesan produk ini.
                </div>
            @else
                @if($me->isBuyer())
                    @if($product->status === 'approved' && $product->stock > 0)
                        <form method="POST" action="{{ route('cart.store', $product) }}" class="row g-2 align-items-end mb-3">
                            @csrf
                            <div class="col-auto">
                                <label class="form-label small mb-1">Jumlah</label>
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control" style="width:100px">
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-primary" type="submit"><i class="bi bi-cart-plus"></i> Tambah ke Keranjang</button>
                            </div>
                        </form>
                    @endif

                    <div class="d-flex gap-2">
                        <a href="{{ route('chat.show', $sellerUser) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-chat-dots"></i> Chat Penjual</a>
                        <a href="{{ route('complaints.index', ['product' => $product->id, 'user' => $sellerUser->id]) }}" class="btn btn-outline-danger btn-sm"><i class="bi bi-flag"></i> Laporkan</a>
                    </div>
                @elseif($isOwner)
                    <a href="{{ route('seller.products.edit', $product) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit Produk</a>
                @elseif($me->isSeller())
                    <a href="{{ route('complaints.index', ['product' => $product->id, 'user' => $sellerUser->id]) }}" class="btn btn-outline-danger btn-sm"><i class="bi bi-flag"></i> Laporkan</a>
                @elseif($me->isAdmin())
                    <a href="{{ route('admin.products.index', ['q' => $product->name]) }}" class="btn btn-outline-primary btn-sm">Kelola di panel admin</a>
                @endif
            @endguest
        </div>
    </div>

    {{-- Ulasan toko --}}
    <div class="card mt-4">
        <div class="card-header">Ulasan untuk {{ $product->store->name }}</div>
        <div class="card-body">
            @forelse($reviews as $review)
                <div class="d-flex gap-3 {{ ! $loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                    <span class="avatar">{{ $review->buyer->initials }}</span>
                    <div>
                        <div class="fw-semibold">{{ $review->buyer->name }}</div>
                        @include('partials.stars', ['value' => $review->rating])
                        <span class="text-muted small ms-1">{{ $review->created_at->diffForHumans() }}</span>
                        @if($review->comment)<p class="mb-0 mt-1">{{ $review->comment }}</p>@endif
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">Belum ada ulasan.</p>
            @endforelse
        </div>
    </div>

    @if($related->isNotEmpty())
        <h5 class="mt-4 mb-3">Produk serupa</h5>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            @foreach($related as $item)
                <div class="col">
                    @include('partials.product-card', ['product' => $item->loadMissing('store', 'category')])
                </div>
            @endforeach
        </div>
    @endif
@endsection
