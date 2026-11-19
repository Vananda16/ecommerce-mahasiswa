@extends('layouts.app')

@section('title', 'Chat dengan '.$partner->name)

@section('content')
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('chat.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
        <span class="avatar">{{ $partner->initials }}</span>
        <div class="lh-sm">
            <div class="fw-bold">{{ $partner->isSeller() && $partner->store ? $partner->store->name : $partner->name }}</div>
            <small class="text-muted">{{ $partner->role_label }}{{ $partner->isSeller() && $partner->store ? ' · '.$partner->name : '' }}</small>
        </div>
        <a href="{{ route('complaints.index', ['user' => $partner->id]) }}" class="btn btn-outline-danger btn-sm ms-auto" title="Laporkan pengguna">
            <i class="bi bi-flag"></i>
        </a>
    </div>

    <div class="card">
        <div class="card-body chat-box" id="chat-box">
            @forelse($messages as $message)
                @php $mine = $message->sender_id === auth()->id(); @endphp
                <div class="d-flex {{ $mine ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                    <div>
                        <div class="bubble {{ $mine ? 'me' : 'them' }}">{{ $message->body }}</div>
                        <div class="text-muted {{ $mine ? 'text-end' : '' }}" style="font-size:.7rem">
                            {{ $message->created_at->translatedFormat('d M, H:i') }}
                            @if($mine && $message->read_at) · dibaca @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted my-4">Belum ada pesan. Mulai percakapan di bawah.</p>
            @endforelse
        </div>

        <div class="card-footer bg-white">
            <form method="POST" action="{{ route('chat.store', $partner) }}" class="d-flex gap-2">
                @csrf
                <input type="text" name="body" class="form-control @error('body') is-invalid @enderror"
                       placeholder="Tulis pesan…" maxlength="2000" required autofocus autocomplete="off">
                <button class="btn btn-primary" type="submit"><i class="bi bi-send"></i></button>
            </form>
            @error('body')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            <div class="form-text">Halaman tidak memuat ulang otomatis — muat ulang untuk melihat balasan terbaru.</div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const box = document.getElementById('chat-box');
        if (box) box.scrollTop = box.scrollHeight;
    </script>
@endpush
