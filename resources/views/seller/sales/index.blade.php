@extends('layouts.app')

@section('title', 'Riwayat Penjualan')

@section('content')
    <h3 class="fw-bold mb-3">Riwayat Penjualan</h3>

    <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-auto">
            <label class="form-label small mb-1">Dari</label>
            <input type="date" name="from" value="{{ $from->toDateString() }}" class="form-control">
        </div>
        <div class="col-auto">
            <label class="form-label small mb-1">Sampai</label>
            <input type="date" name="to" value="{{ $to->toDateString() }}" class="form-control">
        </div>
        <div class="col-auto"><button class="btn btn-outline-primary">Terapkan</button></div>
        @error('to')<div class="col-12 text-danger small">{{ $message }}</div>@enderror
    </form>

    <div class="row g-3 mb-3">
        <div class="col-sm-6">
            <div class="card"><div class="card-body">
                <div class="text-muted small">Pesanan selesai</div>
                <div class="fs-3 fw-bold">{{ $totalOrders }}</div>
            </div></div>
        </div>
        <div class="col-sm-6">
            <div class="card"><div class="card-body">
                <div class="text-muted small">Total penjualan</div>
                <div class="fs-3 fw-bold text-success">@rupiah($totalRevenue)</div>
            </div></div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Tanggal</th><th>Kode</th><th>Pembeli</th><th>Item</th><th class="text-end">Total</th><th></th></tr></thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="text-muted small">{{ $order->created_at->translatedFormat('d M Y') }}</td>
                            <td class="fw-semibold">{{ $order->code }}</td>
                            <td>{{ $order->buyer->name }}</td>
                            <td>{{ $order->items->sum('quantity') }}</td>
                            <td class="text-end">@rupiah($order->total)</td>
                            <td class="text-end"><a href="{{ route('seller.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">Belum ada penjualan pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $orders->links() }}</div>
@endsection
