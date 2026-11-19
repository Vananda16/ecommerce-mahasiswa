@extends('layouts.app')

@section('title', 'Pesanan '.$order->code)

@section('content')
    @php
        $payment = $order->payment;
        $canUploadProof = ! $payment->isCod() && ! $order->isClosed() && $payment->status !== 'paid';
    @endphp

    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
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

            {{-- Ulasan --}}
            @if($order->status === 'completed')
                <div class="card">
                    <div class="card-header">Ulasan untuk penjual</div>
                    <div class="card-body">
                        @if($order->review)
                            @include('partials.stars', ['value' => $order->review->rating])
                            <p class="mb-0 mt-1">{{ $order->review->comment ?: 'Tanpa komentar.' }}</p>
                        @else
                            <form method="POST" action="{{ route('reviews.store', $order) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label d-block">Rating <span class="text-danger">*</span></label>
                                    @for($i = 5; $i >= 1; $i--)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="rating" id="rating-{{ $i }}" value="{{ $i }}" @checked(old('rating') == $i)>
                                            <label class="form-check-label text-warning" for="rating-{{ $i }}">{{ $i }} <i class="bi bi-star-fill"></i></label>
                                        </div>
                                    @endfor
                                    @error('rating')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                <x-textarea name="comment" label="Komentar" rows="3" />
                                <button class="btn btn-primary" type="submit">Kirim ulasan</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">Penjual</div>
                <div class="card-body">
                    <div class="fw-semibold">{{ $order->store->name }}</div>
                    <div class="text-muted small mb-2">{{ $order->store->user->name }}</div>
                    <a href="{{ route('chat.show', $order->store->user) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-chat-dots"></i> Chat penjual</a>
                </div>
            </div>

            @unless($payment->isCod())
                <div class="card mb-4">
                    <div class="card-header">Pembayaran</div>
                    <div class="card-body">
                        @if($order->store->payment_info)
                            <div class="alert alert-light border small">
                                <div class="fw-semibold mb-1">Tujuan pembayaran</div>
                                {{ $order->store->payment_info }}
                                <div class="mt-1">Nominal: <strong>@rupiah($order->total)</strong></div>
                            </div>
                        @else
                            <div class="alert alert-warning small">Penjual belum mengisi info rekening. Tanyakan lewat chat.</div>
                        @endif

                        @if($payment->proof_url)
                            <a href="{{ $payment->proof_url }}" target="_blank" rel="noopener">
                                <img src="{{ $payment->proof_url }}" class="img-fluid rounded border mb-2" alt="Bukti pembayaran">
                            </a>
                        @endif

                        @if($canUploadProof)
                            <form method="POST" action="{{ route('orders.pay', $order) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small">{{ $payment->proof ? 'Unggah ulang bukti' : 'Unggah bukti pembayaran' }}</label>
                                    <input type="file" name="proof" accept="image/*" class="form-control @error('proof') is-invalid @enderror" required>
                                    @error('proof')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <x-input name="note" label="Catatan (opsional)" />
                                <button class="btn btn-primary btn-sm w-100" type="submit">Kirim konfirmasi pembayaran</button>
                            </form>
                        @elseif($payment->status === 'paid')
                            <div class="text-success"><i class="bi bi-check-circle"></i> Pembayaran lunas{{ $payment->paid_at ? ' pada '.$payment->paid_at->translatedFormat('d M Y') : '' }}.</div>
                        @endif
                    </div>
                </div>
            @else
                <div class="alert alert-info small"><i class="bi bi-cash-coin"></i> Bayar langsung ke penjual saat barang/jasa diterima.</div>
            @endunless

            @if($order->status === 'pending')
                <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Batalkan pesanan ini?')">
                    @csrf
                    <button class="btn btn-outline-danger w-100"><i class="bi bi-x-circle"></i> Batalkan pesanan</button>
                </form>
            @endif
        </div>
    </div>
@endsection
