@extends('layouts.app')

@section('title', 'Chat')

@section('content')
    <h3 class="fw-bold mb-4">Chat</h3>

    <div class="card">
        <div class="list-group list-group-flush">
            @forelse($conversations as $c)
                @php $partner = $c['user']; @endphp
                <a href="{{ route('chat.show', $partner) }}" class="list-group-item list-group-item-action d-flex gap-3 align-items-center py-3">
                    <span class="avatar">{{ $partner->initials }}</span>
                    <div class="flex-grow-1" style="min-width:0">
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold">
                                {{ $partner->isSeller() && $partner->store ? $partner->store->name : $partner->name }}
                                <span class="badge text-bg-light border ms-1">{{ $partner->role_label }}</span>
                            </span>
                            <small class="text-muted">{{ $c['last']?->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="text-muted small text-truncate">
                            {{ $c['last']?->sender_id === auth()->id() ? 'Anda: ' : '' }}{{ $c['last']?->body }}
                        </div>
                    </div>
                    @if($c['unread'] > 0)
                        <span class="badge rounded-pill bg-danger">{{ $c['unread'] }}</span>
                    @endif
                </a>
            @empty
                <div class="list-group-item text-center text-muted py-5">
                    <i class="bi bi-chat-dots fs-1"></i>
                    <p class="mb-0 mt-2">
                        Belum ada percakapan.
                        @if(auth()->user()->isBuyer())
                            Buka halaman produk lalu klik <strong>Chat Penjual</strong>.
                        @else
                            Pembeli akan menghubungi Anda dari halaman produk.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
