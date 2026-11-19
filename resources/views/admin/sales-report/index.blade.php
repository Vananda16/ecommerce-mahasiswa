@extends('layouts.app')

@section('title', 'Laporan Penjualan & Pembelian')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h3 class="fw-bold mb-0">Laporan Penjualan &amp; Pembelian</h3>
        <button class="btn btn-outline-secondary no-print" onclick="window.print()"><i class="bi bi-printer"></i> Cetak / Simpan PDF</button>
    </div>

    <form method="GET" class="row g-2 align-items-end mb-4 no-print">
        <div class="col-auto">
            <label class="form-label small mb-1">Dari</label>
            <input type="date" name="from" value="{{ $from->toDateString() }}" class="form-control">
        </div>
        <div class="col-auto">
            <label class="form-label small mb-1">Sampai</label>
            <input type="date" name="to" value="{{ $to->toDateString() }}" class="form-control">
        </div>
        <div class="col-auto"><button class="btn btn-primary">Tampilkan</button></div>
        @error('to')<div class="col-12 text-danger small">{{ $message }}</div>@enderror
    </form>

    <p class="text-muted">Periode {{ $from->translatedFormat('d F Y') }} – {{ $to->translatedFormat('d F Y') }} · hanya pesanan berstatus <strong>Selesai</strong>.</p>

    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="card"><div class="card-body">
            <div class="text-muted small">Transaksi selesai</div><div class="fs-3 fw-bold">{{ $orders->count() }}</div>
        </div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body">
            <div class="text-muted small">Total perputaran uang</div><div class="fs-3 fw-bold text-success">@rupiah($grandTotal)</div>
        </div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body">
            <div class="text-muted small">Penjual aktif / Pembeli aktif</div><div class="fs-3 fw-bold">{{ $bySeller->count() }} / {{ $byBuyer->count() }}</div>
        </div></div></div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">Laporan penjualan (per toko)</div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light"><tr><th>Toko</th><th class="text-center">Pesanan</th><th class="text-center">Item</th><th class="text-end">Penjualan</th></tr></thead>
                        <tbody>
                            @forelse($bySeller as $row)
                                <tr>
                                    <td>{{ $row['name'] }}</td>
                                    <td class="text-center">{{ $row['orders'] }}</td>
                                    <td class="text-center">{{ $row['items'] }}</td>
                                    <td class="text-end">@rupiah($row['total'])</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">Laporan pembelian (per pembeli)</div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light"><tr><th>Pembeli</th><th class="text-center">Pesanan</th><th class="text-center">Item</th><th class="text-end">Pembelian</th></tr></thead>
                        <tbody>
                            @forelse($byBuyer as $row)
                                <tr>
                                    <td>{{ $row['name'] }} @if($row['nim'])<span class="small text-muted">· {{ $row['nim'] }}</span>@endif</td>
                                    <td class="text-center">{{ $row['orders'] }}</td>
                                    <td class="text-center">{{ $row['items'] }}</td>
                                    <td class="text-end">@rupiah($row['total'])</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Rincian transaksi</div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light"><tr><th>Tanggal</th><th>Kode</th><th>Toko</th><th>Pembeli</th><th class="text-end">Total</th></tr></thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="small text-muted">{{ $order->created_at->translatedFormat('d M Y') }}</td>
                            <td>{{ $order->code }}</td>
                            <td>{{ $order->store->name }}</td>
                            <td>{{ $order->buyer->name }}</td>
                            <td class="text-end">@rupiah($order->total)</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada transaksi selesai pada periode ini.</td></tr>
                    @endforelse
                </tbody>
                @if($orders->isNotEmpty())
                    <tfoot><tr class="fw-bold"><td colspan="4" class="text-end">Total</td><td class="text-end">@rupiah($grandTotal)</td></tr></tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
