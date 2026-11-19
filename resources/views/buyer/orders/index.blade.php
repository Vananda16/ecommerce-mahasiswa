@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
    <h3 class="fw-bold mb-3">Pesanan Saya</h3>

    <ul class="nav nav-pills mb-3 gap-1">
        <li class="nav-item"><a class="nav-link {{ ! request('status') ? 'active' : '' }}" href="{{ route('orders.index') }}">Semua</a></li>
        @foreach(\App\Models\Order::STATUSES as $key => $meta)
            <li class="nav-item">
                <a class="nav-link {{ request('status') === $key ? 'active' : '' }}" href="{{ route('orders.index', ['status' => $key]) }}">{{ $meta['label'] }}</a>
            </li>
        @endforeach
    </ul>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Toko</th>
                        <th>Item</th>
                        <th class="text-end">Total</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                        <th>Tanggal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="fw-semibold">{{ $order->code }}</td>
                            <td>{{ $order->store->name }}</td>
                            <td>{{ $order->items->sum('quantity') }}</td>
                            <td class="text-end">@rupiah($order->total)</td>
                            <td><span class="badge text-bg-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                            <td><span class="badge text-bg-{{ $order->payment->status_color }}">{{ $order->payment->status_label }}</span></td>
                            <td class="text-muted small">{{ $order->created_at->translatedFormat('d M Y') }}</td>
                            <td class="text-end"><a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $orders->links() }}</div>
@endsection
