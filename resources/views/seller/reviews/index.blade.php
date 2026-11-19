@extends('layouts.app')

@section('title', 'Ulasan & Rating')

@section('content')
    <h3 class="fw-bold mb-4">Ulasan &amp; Rating</h3>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="display-4 fw-bold">{{ number_format($store->average_rating, 1) }}</div>
                    @include('partials.stars', ['value' => $store->average_rating])
                    <div class="text-muted small mb-3">dari {{ $count }} ulasan</div>

                    @for($star = 5; $star >= 1; $star--)
                        @php
                            $total = $distribution[$star] ?? 0;
                            $pct = $count > 0 ? round($total / $count * 100) : 0;
                        @endphp
                        <div class="d-flex align-items-center gap-2 small mb-1">
                            <span style="width:28px">{{ $star }} <i class="bi bi-star-fill text-warning"></i></span>
                            <div class="progress flex-grow-1" style="height:8px">
                                <div class="progress-bar bg-warning" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="text-muted" style="width:28px">{{ $total }}</span>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Ulasan pembeli</div>
                <div class="list-group list-group-flush">
                    @forelse($reviews as $review)
                        <div class="list-group-item py-3">
                            <div class="d-flex gap-3">
                                <span class="avatar">{{ $review->buyer->initials }}</span>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $review->buyer->name }}</strong>
                                        <small class="text-muted">{{ $review->created_at->translatedFormat('d M Y') }}</small>
                                    </div>
                                    @include('partials.stars', ['value' => $review->rating])
                                    <p class="mb-1">{{ $review->comment ?: 'Tanpa komentar.' }}</p>
                                    <a href="{{ route('seller.orders.show', $review->order_id) }}" class="small text-decoration-none">
                                        Pesanan {{ $review->order->code }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted py-5">Belum ada ulasan.</div>
                    @endforelse
                </div>
            </div>
            <div class="mt-3">{{ $reviews->links() }}</div>
        </div>
    </div>
@endsection
