@php
    $store = auth()->user()->store;
    $newOrders = $store ? $store->orders()->where('status', 'pending')->count() : 0;
@endphp

<nav class="nav flex-column">
    <a class="nav-link {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}" href="{{ route('seller.dashboard') }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <div class="nav-heading">Toko</div>
    <a class="nav-link {{ request()->routeIs('seller.store.*') ? 'active' : '' }}" href="{{ route('seller.store.edit') }}">
        <i class="bi bi-shop-window"></i> Profil Toko
    </a>
    <a class="nav-link {{ request()->routeIs('seller.products.*') ? 'active' : '' }}" href="{{ route('seller.products.index') }}">
        <i class="bi bi-box-seam"></i> Produk Saya
    </a>

    <div class="nav-heading">Penjualan</div>
    <a class="nav-link {{ request()->routeIs('seller.orders.*') ? 'active' : '' }}" href="{{ route('seller.orders.index') }}">
        <i class="bi bi-bag-check"></i> Pesanan Masuk
        @if($newOrders > 0)<span class="badge bg-danger ms-auto">{{ $newOrders }}</span>@endif
    </a>
    <a class="nav-link {{ request()->routeIs('seller.sales.*') ? 'active' : '' }}" href="{{ route('seller.sales.index') }}">
        <i class="bi bi-receipt"></i> Riwayat Penjualan
    </a>
    <a class="nav-link {{ request()->routeIs('seller.reviews.*') ? 'active' : '' }}" href="{{ route('seller.reviews.index') }}">
        <i class="bi bi-star"></i> Ulasan &amp; Rating
    </a>

    <div class="nav-heading">Lainnya</div>
    <a class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}" href="{{ route('chat.index') }}">
        <i class="bi bi-chat-dots"></i> Chat Pembeli
        @if(($unreadChat ?? 0) > 0)<span class="badge bg-danger ms-auto">{{ $unreadChat }}</span>@endif
    </a>
    <a class="nav-link {{ request()->routeIs('complaints.*') ? 'active' : '' }}" href="{{ route('complaints.index') }}">
        <i class="bi bi-flag"></i> Pengaduan
    </a>
    <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
        <i class="bi bi-person"></i> Profil Saya
    </a>
    <a class="nav-link" href="{{ route('home') }}">
        <i class="bi bi-grid"></i> Lihat Katalog
    </a>
</nav>
