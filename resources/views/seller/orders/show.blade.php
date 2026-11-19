@extends('layouts.app')

@section('title', 'Pesanan '.$order->code)

@section('content')
    @php $payment = $order->payment; @endphp

    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('seller.orders.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
        <h3 class="fw-bold mb-0">Pesanan {{ $order->code }}</h3>
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
            {{-- Pembeli --}}
            <div class="card mb-4">
                <div class="card-header">Pembeli</div>
                <div class="card-body">
                    <div class="fw-semibold">{{ $order->buyer->name }}</div>
                    @if($order->buyer->nim)<div class="small text-muted">NIM {{ $order->buyer->nim }}</div>@endif
                    @if($order->buyer->phone)<div class="small"><i class="bi bi-telephone"></i> {{ $order->buyer->phone }}</div>@endif
                    <a href="{{ route('chat.show', $order->buyer) }}" class="btn btn-outline-primary btn-sm mt-2"><i class="bi bi-chat-dots"></i> Chat pembeli</a>
                </div>
            </div>

            {{-- Aksi status pesanan --}}
            <div class="card mb-4">
                <div class="card-header">Tindakan</div>
                <div class="card-body">
                    @if($order->status === 'pending')
                        <form method="POST" action="{{ route('seller.orders.accept', $order) }}" class="mb-2">
                            @csrf
                            <button class="btn btn-success w-100"><i class="bi bi-check-lg"></i> Terima pesanan</button>
                        </form>

                        <button class="btn btn-outline-danger w-100" type="button" data-bs-toggle="collapse" data-bs-target="#rejectForm">
                            <i class="bi bi-x-lg"></i> Tolak pesanan
                        </button>
                        <div class="collapse mt-2 {{ $errors->has('rejection_reason') ? 'show' : '' }}" id="rejectForm">
                            <form method="POST" action="{{ route('seller.orders.reject', $order) }}">
                                @csrf
                                <x-textarea name="rejection_reason" label="Alasan penolakan" rows="2" :required="true" />
                                <button class="btn btn-danger btn-sm w-100">Kirim penolakan</button>
                            </form>
                        </div>
                    @elseif($order->next_status)
                        <form method="POST" action="{{ route('seller.orders.advance', $order) }}">
                            @csrf
                            <button class="btn btn-primary w-100">
                                Ubah status → {{ \App\Models\Order::STATUSES[$order->next_status]['label'] }}
                            </button>
                        </form>
                        @if($order->next_status === 'shipped' && ! $payment->isCod() && $payment->status !== 'paid')
                            <div class="form-text text-warning">Konfirmasi pembayaran dulu sebelum mengirim.</div>
                        @endif
                    @else
                        <p class="text-muted mb-0">Tidak ada tindakan lagi untuk pesanan ini.</p>
                    @endif
                </div>
            </div>

            {{-- Verifikasi pembayaran --}}
            <div class="card">
                <div class="card-header">Pembayaran</div>
                <div class="card-body">
                    <div class="mb-2">
                        {{ $payment->method_label }}
                        <span class="badge text-bg-{{ $payment->status_color }}">{{ $payment->status_label }}</span>
                    </div>

                    @if($payment->proof_url)
                        <a href="{{ $payment->proof_url }}" target="_blank" rel="noopener">
                            <img src="{{ $payment->proof_url }}" class="img-fluid rounded border mb-2" alt="Bukti pembayaran">
                        </a>
                        @if($payment->note && $payment->status === 'waiting')
                            <div class="small text-muted mb-2">Catatan pembeli: {{ $payment->note }}</div>
                        @endif
                    @elseif(! $payment->isCod())
                        <div class="small text-muted">Belum ada bukti pembayaran.</div>
                    @endif

                    @if($payment->status === 'waiting')
                        <form method="POST" action="{{ route('seller.orders.payment.confirm', $order) }}" class="mb-2">
                            @csrf
                            <button class="btn btn-success btn-sm w-100"><i class="bi bi-check2-circle"></i> Konfirmasi lunas</button>
                        </form>
                        <button class="btn btn-outline-danger btn-sm w-100" type="button" data-bs-toggle="collapse" data-bs-target="#rejectPayment">Tolak bukti</button>
                        <div class="collapse mt-2 {{ $errors->has('note') ? 'show' : '' }}" id="rejectPayment">
                            <form method="POST" action="{{ route('seller.orders.payment.reject', $order) }}">
                                @csrf
                                <x-input name="note" label="Alasan penolakan bukti" :required="true" />
                                <button class="btn btn-danger btn-sm w-100">Tolak</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
