@extends('layouts.app')

@section('title', 'Pengaduan Pengguna')

@section('content')
    <h3 class="fw-bold mb-3">Pengaduan Pengguna</h3>

    <ul class="nav nav-pills mb-3 gap-1">
        <li class="nav-item"><a class="nav-link {{ ! request('status') ? 'active' : '' }}" href="{{ route('admin.complaints.index') }}">Semua</a></li>
        @foreach(\App\Models\Report::STATUSES as $key => $meta)
            <li class="nav-item">
                <a class="nav-link {{ request('status') === $key ? 'active' : '' }}" href="{{ route('admin.complaints.index', ['status' => $key]) }}">{{ $meta['label'] }}</a>
            </li>
        @endforeach
    </ul>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Judul</th><th>Pelapor</th><th>Terlapor / Produk</th><th>Status</th><th>Tanggal</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td class="fw-semibold">{{ \Illuminate\Support\Str::limit($report->subject, 40) }}</td>
                            <td>{{ $report->reporter->name }}</td>
                            <td class="small">
                                @if($report->reportedUser)<div><i class="bi bi-person"></i> {{ $report->reportedUser->name }}</div>@endif
                                @if($report->product)<div><i class="bi bi-box-seam"></i> {{ \Illuminate\Support\Str::limit($report->product->name, 28) }}</div>@endif
                                @if(! $report->reportedUser && ! $report->product)<span class="text-muted">-</span>@endif
                            </td>
                            <td><span class="badge text-bg-{{ $report->status_color }}">{{ $report->status_label }}</span></td>
                            <td class="small text-muted">{{ $report->created_at->translatedFormat('d M Y, H:i') }}</td>
                            <td class="text-end"><a href="{{ route('admin.complaints.show', $report) }}" class="btn btn-sm btn-outline-primary">Tinjau</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">Tidak ada pengaduan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $reports->links() }}</div>
@endsection
