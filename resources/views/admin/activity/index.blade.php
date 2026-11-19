@extends('layouts.app')

@section('title', 'Pantau Aktivitas')

@section('content')
    <h3 class="fw-bold mb-4">Pantau Aktivitas Jual Beli</h3>

    <div class="row g-3 mb-4">
        @foreach([
            ['Pesanan hari ini', $today['orders'], 'bag-check', 'primary'],
            ['Pendaftar hari ini', $today['users'], 'person-plus', 'success'],
            ['Produk baru hari ini', $today['products'], 'box-seam', 'warning'],
            ['Pesan chat hari ini', $today['messages'], 'chat-dots', 'info'],
        ] as [$label, $value, $icon, $color])
            <div class="col-6 col-xl-3">
                <div class="card h-100"><div class="card-body d-flex gap-3 align-items-center">
                    <span class="stat-icon bg-{{ $color }}-subtle text-{{ $color }}"><i class="bi bi-{{ $icon }}"></i></span>
                    <div><div class="text-muted small">{{ $label }}</div><div class="fs-4 fw-bold">{{ $value }}</div></div>
                </div></div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header">Linimasa aktivitas terbaru</div>
        <ul class="list-group list-group-flush">
            @forelse($events as $event)
                <li class="list-group-item d-flex gap-3 align-items-start py-3">
                    <span class="stat-icon bg-{{ $event['color'] }}-subtle text-{{ $event['color'] }}" style="width:38px;height:38px;font-size:1.05rem">
                        <i class="bi bi-{{ $event['icon'] }}"></i>
                    </span>
                    <div class="flex-grow-1">
                        @if($event['url'])
                            <a href="{{ $event['url'] }}" class="text-decoration-none text-reset">{{ $event['text'] }}</a>
                        @else
                            {{ $event['text'] }}
                        @endif
                        <div class="small text-muted">{{ $event['time']->translatedFormat('d M Y, H:i') }} &middot; {{ $event['time']->diffForHumans() }}</div>
                    </div>
                </li>
            @empty
                <li class="list-group-item text-center text-muted py-5">Belum ada aktivitas.</li>
            @endforelse
        </ul>
    </div>
@endsection
