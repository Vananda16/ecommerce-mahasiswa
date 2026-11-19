@extends('layouts.app')

@section('title', 'Dashboard Penjual')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold mb-0">Halo, {{ auth()->user()->name }} 👋</h3>
            <span class="text-muted">{{ $store->name }}</span>
        </div>
        <a href="{{ route('seller.products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Produk</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card h-100"><div class="card-body d-flex gap-3 align-items-center">
                <span class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-bell"></i></span>
                <div><div class="text-muted small">Pesanan baru</div><div class="fs-4 fw-bold">{{ $stats['new_orders'] }}</div></div>
            </div></div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card h-100"><div class="card-body d-flex gap-3 align-items-center">
                <span class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-truck"></i></span>
                <div><div class="text-muted small">Sedang berjalan</div><div class="fs-4 fw-bold">{{ $stats['in_progress'] }}</div></div>
            </div></div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card h-100"><div class="card-body d-flex gap-3 align-items-center">
                <span class="stat-icon bg-success-subtle text-success"><i class="bi bi-cash-stack"></i></span>
                <div><div class="text-muted small">Total penjualan</div><div class="fs-5 fw-bold">@rupiah($stats['revenue'])</div></div>
            </div></div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card h-100"><div class="card-body d-flex gap-3 align-items-center">
                <span class="stat-icon bg-info-subtle text-info"><i class="bi bi-star"></i></span>
                <div>
                    <div class="text-muted small">Rating toko</div>
                    <div class="fs-4 fw-bold">{{ number_format($stats['rating'], 1) }} <small class="text-muted fs-6">({{ $stats['reviews'] }})</small></div>
                </div>
            </div></div>
        </div>
    </div>

    @if($stats['pending_products'] > 0)
        <div class="alert alert-warning">
            <i class="bi bi-hourglass-split"></i>
            {{ $stats['pending_products'] }} produk menunggu verifikasi admin.
            <a href="{{ route('seller.products.index', ['status' => 'pending']) }}">Lihat</a>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span>Pesanan terbaru</span>
                    <a href="{{ route('seller.orders.index') }}" class="small text-decoration-none">Lihat semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Kode</th><th>Pembeli</th><th class="text-end">Total</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td class="fw-semibold">{{ $order->code }}</td>
                                    <td>{{ $order->buyer->name }}</td>
                                    <td class="text-end">@rupiah($order->total)</td>
                                    <td><span class="badge text-bg-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                                    <td class="text-end"><a href="{{ route('seller.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Stok menipis</div>
                <ul class="list-group list-group-flush">
                    @forelse($lowStock as $product)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('seller.products.edit', $product) }}" class="text-decoration-none">{{ \Illuminate\Support\Str::limit($product->name, 28) }}</a>
                            <span class="badge {{ $product->stock === 0 ? 'bg-danger' : 'bg-warning text-dark' }}">{{ $product->stock }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Semua stok aman 👍</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
