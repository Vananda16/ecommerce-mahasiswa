@extends('layouts.app')

@section('title', 'Pengaduan')

@section('content')
    <h3 class="fw-bold mb-4">Pengaduan</h3>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">Kirim pengaduan ke admin</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('complaints.store') }}">
                        @csrf

                        @if($product)
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="alert alert-light border py-2 small">
                                <i class="bi bi-box-seam"></i> Produk dilaporkan: <strong>{{ $product->name }}</strong>
                            </div>
                        @endif

                        @if($reportedUser)
                            <input type="hidden" name="reported_user_id" value="{{ $reportedUser->id }}">
                            <div class="alert alert-light border py-2 small">
                                <i class="bi bi-person"></i> Pengguna dilaporkan: <strong>{{ $reportedUser->name }}</strong>
                            </div>
                        @endif

                        <x-input name="subject" label="Judul pengaduan" :required="true" />
                        <x-textarea name="description" label="Uraian" rows="5" :required="true"
                                    help="Jelaskan kejadian secara jelas (kapan, apa yang terjadi, bukti bila ada)." />

                        <button class="btn btn-danger" type="submit"><i class="bi bi-send"></i> Kirim pengaduan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">Pengaduan saya</div>
                <div class="list-group list-group-flush">
                    @forelse($reports as $report)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $report->subject }}</strong>
                                <span class="badge text-bg-{{ $report->status_color }}">{{ $report->status_label }}</span>
                            </div>
                            <div class="small text-muted mb-1">
                                {{ $report->created_at->translatedFormat('d M Y, H:i') }}
                                @if($report->reportedUser) · terlapor: {{ $report->reportedUser->name }} @endif
                                @if($report->product) · produk: {{ $report->product->name }} @endif
                            </div>
                            <div style="white-space:pre-line">{{ $report->description }}</div>
                            @if($report->admin_note)
                                <div class="alert alert-light border mt-2 mb-0 py-2 small">
                                    <strong>Tanggapan admin:</strong> {{ $report->admin_note }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="list-group-item text-muted text-center py-4">Belum ada pengaduan.</div>
                    @endforelse
                </div>
            </div>
            <div class="mt-3">{{ $reports->links() }}</div>
        </div>
    </div>
@endsection
