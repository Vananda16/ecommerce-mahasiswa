@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <h3 class="fw-bold mb-4">Dashboard Admin</h3>

    <div class="row g-3 mb-4">
        @php
            $cards = [
                ['Penjual', $stats['sellers'], 'shop', 'primary', route('admin.users.index', ['role' => 'seller'])],
                ['Pembeli', $stats['buyers'], 'people', 'info', route('admin.users.index', ['role' => 'buyer'])],
                ['Produk', $stats['products'], 'box-seam', 'secondary', route('admin.products.index')],
                ['Menunggu verifikasi', $stats['pending_products'], 'hourglass-split', 'warning', route('admin.products.index', ['status' => 'pending'])],
                ['Total transaksi', $stats['orders'], 'receipt', 'success', route('admin.transactions.index')],
                ['Transaksi hari ini', $stats['orders_today'], 'calendar-check', 'success', route('admin.transactions.index', ['from' => today()->toDateString()])],
                ['Pengaduan baru', $stats['open_reports'], 'flag', 'danger', route('admin.complaints.index', ['status' => 'open'])],
                ['Akun diblokir', $stats['blocked'], 'slash-circle', 'dark', route('admin.users.index', ['status' => 'blocked'])],
            ];
        @endphp

        @foreach($cards as [$label, $value, $icon, $color, $url])
            <div class="col-6 col-xl-3">
                <a href="{{ $url }}" class="text-decoration-none text-reset">
                    <div class="card h-100"><div class="card-body d-flex gap-3 align-items-center">
                        <span class="stat-icon bg-{{ $color }}-subtle text-{{ $color }}"><i class="bi bi-{{ $icon }}"></i></span>
                        <div><div class="text-muted small">{{ $label }}</div><div class="fs-4 fw-bold">{{ $value }}</div></div>
                    </div></div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <span>Transaksi 6 bulan terakhir</span>
                    <span class="text-muted small">Omzet selesai: <strong>@rupiah($stats['revenue'])</strong></span>
                </div>
                <div class="card-body"><canvas id="ordersChart" height="110"></canvas></div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">Produk menunggu verifikasi</div>
                <ul class="list-group list-group-flush">
                    @forelse($pendingProducts as $product)
                        <li class="list-group-item">
                            <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($product->name, 34) }}</div>
                            <div class="small text-muted">{{ $product->store->name }} &middot; @rupiah($product->price)</div>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Tidak ada antrean 🎉</li>
                    @endforelse
                </ul>
                @if($pendingProducts->isNotEmpty())
                    <div class="card-footer bg-white">
                        <a href="{{ route('admin.products.index', ['status' => 'pending']) }}" class="btn btn-sm btn-warning w-100">Verifikasi sekarang</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Transaksi terbaru</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Kode</th><th>Pembeli</th><th>Toko</th><th class="text-end">Total</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td class="fw-semibold">{{ $order->code }}</td>
                            <td>{{ $order->buyer->name }}</td>
                            <td>{{ $order->store->name }}</td>
                            <td class="text-end">@rupiah($order->total)</td>
                            <td><span class="badge text-bg-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                            <td class="text-end"><a href="{{ route('admin.transactions.show', $order) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const chart = @json($chart);
        new Chart(document.getElementById('ordersChart'), {
            data: {
                labels: chart.labels,
                datasets: [
                    { type: 'bar', label: 'Jumlah transaksi', data: chart.orders, backgroundColor: 'rgba(79,70,229,.75)', yAxisID: 'y', borderRadius: 6 },
                    { type: 'line', label: 'Omzet selesai (Rp)', data: chart.revenue, borderColor: '#10b981', backgroundColor: '#10b981', tension: .3, yAxisID: 'y1' }
                ]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, title: { display: true, text: 'Transaksi' } },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Rp' } }
                }
            }
        });
    </script>
@endpush
