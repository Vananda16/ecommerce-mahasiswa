@extends('layouts.app')

@section('title', 'Transaksi '.$order->code)

@section('content')
    @php $payment = $order->payment; @endphp

    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
        <h3 class="fw-bold mb-0">Transaksi {{ $order->code }}</h3>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">Ringkasan</div>
                <div class="card-body">@include('partials.order-summary', ['order' => $order])</div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Item pesanan</div>
                @include('partials.order-items', ['order' => $order])
            </div>

            @if($order->review)
                <div class="card">
                    <div class="card-header">Ulasan pembeli</div>
                    <div class="card-body">
                        @include('partials.stars', ['value' => $order->review->rating])
                        <p class="mb-0 mt-1">{{ $order->review->comment ?: 'Tanpa komentar.' }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">Pihak terkait</div>
                <div class="card-body small">
                    <div class="text-muted">Pembeli</div>
                    <div class="fw-semibold mb-2">{{ $order->buyer->name }} @if($order->buyer->nim)<span class="text-muted fw-normal">· {{ $order->buyer->nim }}</span>@endif</div>
                    <div class="text-muted">Penjual</div>
                    <div class="fw-semibold">{{ $order->store->name }} <span class="text-muted fw-normal">· {{ $order->store->user->name }}</span></div>
                </div>
            </div>

            @if($payment->proof_url)
                <div class="card mb-4">
                    <div class="card-header">Bukti pembayaran</div>
                    <div class="card-body">
                        <a href="{{ $payment->proof_url }}" target="_blank" rel="noopener">
                            <img src="{{ $payment->proof_url }}" class="img-fluid rounded border" alt="Bukti pembayaran">
                        </a>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">Koreksi transaksi</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.transactions.update', $order) }}">
                        @csrf
                        @method('PUT')

                        <x-select name="status" label="Status pesanan" :required="true">
                            @foreach(\App\Models\Order::STATUSES as $key => $meta)
                                <option value="{{ $key }}" @selected(old('status', $order->status) === $key)>{{ $meta['label'] }}</option>
                            @endforeach
                        </x-select>

                        <x-select name="payment_status" label="Status pembayaran" :required="true">
                            @foreach(\App\Models\Payment::STATUSES as $key => $meta)
                                <option value="{{ $key }}" @selected(old('payment_status', $payment->status) === $key)>{{ $meta['label'] }}</option>
                            @endforeach
                        </x-select>

                        <button class="btn btn-primary w-100" type="submit">Simpan koreksi</button>
                        <div class="form-text">Menolak/membatalkan pesanan mengembalikan stok. Pesanan yang sudah ditolak/dibatalkan tidak bisa diaktifkan kembali.</div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
