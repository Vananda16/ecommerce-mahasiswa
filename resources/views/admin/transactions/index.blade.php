@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
    <h3 class="fw-bold mb-3">Transaksi</h3>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Kode / pembeli / toko…">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">Semua status</option>
                @foreach(\App\Models\Order::STATUSES as $key => $meta)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $meta['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="payment" class="form-select">
                <option value="">Semua pembayaran</option>
                @foreach(\App\Models\Payment::STATUSES as $key => $meta)
                    <option value="{{ $key }}" @selected(request('payment') === $key)>{{ $meta['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><input type="date" name="from" value="{{ request('from') }}" class="form-control" title="Dari tanggal"></div>
        <div class="col-md-2"><input type="date" name="to" value="{{ request('to') }}" class="form-control" title="Sampai tanggal"></div>
        <div class="col-auto"><button class="btn btn-outline-primary">Filter</button></div>
        @error('to')<div class="col-12 text-danger small">{{ $message }}</div>@enderror
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Kode</th><th>Pembeli</th><th>Toko</th><th class="text-end">Total</th><th>Status</th><th>Pembayaran</th><th>Tanggal</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="fw-semibold">{{ $order->code }}</td>
                            <td>{{ $order->buyer->name }}</td>
                            <td>{{ $order->store->name }}</td>
                            <td class="text-end">@rupiah($order->total)</td>
                            <td><span class="badge text-bg-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                            <td><span class="badge text-bg-{{ $order->payment->status_color }}">{{ $order->payment->status_label }}</span></td>
                            <td class="small text-muted">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</td>
                            <td class="text-end"><a href="{{ route('admin.transactions.show', $order) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">Tidak ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $orders->links() }}</div>
@endsection
