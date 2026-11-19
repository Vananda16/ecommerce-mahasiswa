@php
    $pendingProducts = \App\Models\Product::where('status', 'pending')->count();
    $openReports = \App\Models\Report::where('status', 'open')->count();
@endphp

<nav class="nav flex-column">
    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <div class="nav-heading">Kelola Data</div>
    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
        <i class="bi bi-people"></i> Data Mahasiswa
    </a>
    <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
        <i class="bi bi-box-seam"></i> Produk
        @if($pendingProducts > 0)<span class="badge bg-warning text-dark ms-auto">{{ $pendingProducts }}</span>@endif
    </a>
    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
        <i class="bi bi-tags"></i> Kategori
    </a>

    <div class="nav-heading">Transaksi</div>
    <a class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}" href="{{ route('admin.transactions.index') }}">
        <i class="bi bi-cash-stack"></i> Transaksi
    </a>
    <a class="nav-link {{ request()->routeIs('admin.activity.*') ? 'active' : '' }}" href="{{ route('admin.activity.index') }}">
        <i class="bi bi-activity"></i> Pantau Aktivitas
    </a>
    <a class="nav-link {{ request()->routeIs('admin.sales-report.*') ? 'active' : '' }}" href="{{ route('admin.sales-report.index') }}">
        <i class="bi bi-file-earmark-bar-graph"></i> Laporan
    </a>

    <div class="nav-heading">Moderasi</div>
    <a class="nav-link {{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}" href="{{ route('admin.complaints.index') }}">
        <i class="bi bi-flag"></i> Pengaduan
        @if($openReports > 0)<span class="badge bg-danger ms-auto">{{ $openReports }}</span>@endif
    </a>
    <a class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}" href="{{ route('admin.announcements.index') }}">
        <i class="bi bi-megaphone"></i> Pengumuman
    </a>

    <div class="nav-heading">Lainnya</div>
    <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
        <i class="bi bi-person"></i> Profil Saya
    </a>
    <a class="nav-link" href="{{ route('home') }}">
        <i class="bi bi-grid"></i> Lihat Katalog
    </a>
</nav>
