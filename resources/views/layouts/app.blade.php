<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
</head>
<body>
@php
    $user = auth()->user();
    $hasSidebar = $user && in_array($user->role, ['seller', 'admin'], true);
@endphp

<nav class="navbar bg-white border-bottom sticky-top">
    <div class="container-fluid px-3 px-lg-4">
        <div class="d-flex align-items-center">
            @if($hasSidebar)
                <button class="btn btn-outline-secondary btn-sm d-lg-none me-2" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-label="Menu">
                    <i class="bi bi-list fs-5"></i>
                </button>
            @endif
            <a class="navbar-brand fw-bold text-primary mb-0" href="{{ $user ? route($user->homeRoute()) : route('home') }}">
                <i class="bi bi-shop"></i> {{ config('app.name') }}
            </a>
        </div>

        <div class="d-flex align-items-center gap-2">
            @guest
                <a href="{{ route('home') }}" class="btn btn-link text-decoration-none d-none d-sm-inline">Katalog</a>
                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
            @else
                @if($user->isBuyer())
                    <a href="{{ route('home') }}" class="btn btn-link text-decoration-none d-none d-md-inline">Katalog</a>
                    <a href="{{ route('orders.index') }}" class="btn btn-link text-decoration-none d-none d-md-inline">Pesanan</a>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary btn-sm position-relative" title="Keranjang">
                        <i class="bi bi-cart3"></i>
                        @if($cartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $cartCount }}</span>
                        @endif
                    </a>
                @endif

                @unless($user->isAdmin())
                    <a href="{{ route('chat.index') }}" class="btn btn-outline-secondary btn-sm position-relative" title="Chat">
                        <i class="bi bi-chat-dots"></i>
                        @if($unreadChat > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $unreadChat }}</span>
                        @endif
                    </a>
                @endunless

                <div class="dropdown">
                    <button class="btn btn-light d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar">{{ $user->initials }}</span>
                        <span class="d-none d-md-inline text-start lh-sm">
                            <span class="d-block small fw-semibold">{{ $user->name }}</span>
                            <span class="d-block text-muted" style="font-size:.7rem">{{ $user->role_label }}</span>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profil Saya</a></li>
                        @unless($user->isAdmin())
                            <li><a class="dropdown-item" href="{{ route('complaints.index') }}"><i class="bi bi-flag me-2"></i>Pengaduan</a></li>
                        @endunless
                        <li><a class="dropdown-item" href="{{ route('home') }}"><i class="bi bi-grid me-2"></i>Lihat Katalog</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endguest
        </div>
    </div>
</nav>

@if($hasSidebar)
    <div class="d-lg-flex">
        <aside class="offcanvas-lg offcanvas-start sidebar" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title" id="sidebarMenuLabel">Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Tutup"></button>
            </div>
            <div class="offcanvas-body d-block p-2">
                @include('partials.sidebar-'.$user->role)
            </div>
        </aside>

        <main class="flex-grow-1 p-3 p-lg-4" style="min-width:0">
            @include('partials.announcements')
            @include('partials.flash')
            @yield('content')
        </main>
    </div>
@else
    <main class="container py-4">
        @include('partials.announcements')
        @include('partials.flash')
        @yield('content')
    </main>
@endif

<footer class="text-center text-muted small py-4 no-print">
    &copy; {{ date('Y') }} {{ config('app.name') }} &middot; Platform jual beli antar mahasiswa
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
