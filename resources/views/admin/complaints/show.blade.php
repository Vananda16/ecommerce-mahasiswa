@extends('layouts.app')

@section('title', 'Tinjau Pengaduan')

@section('content')
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('admin.complaints.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
        <h3 class="fw-bold mb-0">Tinjau Pengaduan</h3>
        <span class="badge text-bg-{{ $report->status_color }} ms-1">{{ $report->status_label }}</span>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="fw-bold">{{ $report->subject }}</h5>
                    <div class="small text-muted mb-3">
                        Dikirim oleh <strong>{{ $report->reporter->name }}</strong> ({{ $report->reporter->role_label }})
                        pada {{ $report->created_at->translatedFormat('d M Y, H:i') }}
                    </div>
                    <div style="white-space:pre-line">{{ $report->description }}</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Tanggapan admin</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.complaints.update', $report) }}">
                        @csrf
                        @method('PUT')

                        <x-select name="status" label="Status" :required="true">
                            @foreach(\App\Models\Report::STATUSES as $key => $meta)
                                <option value="{{ $key }}" @selected(old('status', $report->status) === $key)>{{ $meta['label'] }}</option>
                            @endforeach
                        </x-select>

                        <x-textarea name="admin_note" label="Catatan / tanggapan untuk pelapor" :value="$report->admin_note" rows="4" />

                        <button class="btn btn-primary" type="submit">Simpan</button>
                        @if($report->handler)
                            <span class="text-muted small ms-2">Terakhir ditangani oleh {{ $report->handler->name }}</span>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            @if($report->reportedUser)
                @php $target = $report->reportedUser; @endphp
                <div class="card mb-4">
                    <div class="card-header">Akun terlapor</div>
                    <div class="card-body">
                        <div class="fw-semibold">{{ $target->name }}</div>
                        <div class="small text-muted mb-2">{{ $target->email }} · {{ $target->role_label }}</div>

                        @if($target->isAdmin())
                            <div class="text-muted small">Akun admin tidak dapat diblokir dari sini.</div>
                        @elseif($target->is_blocked)
                            <div class="alert alert-danger py-2 small">Sudah diblokir: {{ $target->blocked_reason }}</div>
                            <form method="POST" action="{{ route('admin.users.unblock', $target) }}">
                                @csrf
                                <button class="btn btn-outline-success btn-sm">Buka blokir</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.users.block', $target) }}">
                                @csrf
                                <label class="form-label small">Alasan pemblokiran</label>
                                <input type="text" name="blocked_reason" class="form-control form-control-sm mb-2" maxlength="255"
                                       value="{{ 'Pengaduan: '.$report->subject }}" required>
                                <button class="btn btn-warning btn-sm"><i class="bi bi-slash-circle"></i> Blokir akun</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            @if($report->product)
                <div class="card">
                    <div class="card-header">Produk terlapor</div>
                    <div class="card-body">
                        <a href="{{ route('products.show', $report->product) }}" target="_blank" class="fw-semibold text-decoration-none">{{ $report->product->name }}</a>
                        <div class="small text-muted mb-2">{{ $report->product->store->name }} · @rupiah($report->product->price)</div>
                        <form method="POST" action="{{ route('admin.products.destroy', $report->product) }}"
                              onsubmit="return confirm('Hapus produk ini karena melanggar aturan?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Hapus produk</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
