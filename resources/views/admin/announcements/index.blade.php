@extends('layouts.app')

@section('title', 'Pengumuman')

@section('content')
    <h3 class="fw-bold mb-4">Informasi &amp; Pengumuman Sistem</h3>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Buat pengumuman</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.announcements.store') }}">
                        @csrf
                        <x-input name="title" label="Judul" :required="true" />
                        <x-textarea name="body" label="Isi pengumuman" rows="4" :required="true" />
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active_new" value="1" checked>
                            <label class="form-check-label" for="is_active_new">Tampilkan ke pengguna</label>
                        </div>
                        <button class="btn btn-primary" type="submit"><i class="bi bi-megaphone"></i> Publikasikan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            @forelse($announcements as $announcement)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-bold mb-0">{{ $announcement->title }}</h6>
                                <small class="text-muted">
                                    {{ $announcement->created_at->translatedFormat('d M Y, H:i') }}
                                    @if($announcement->author) · {{ $announcement->author->name }} @endif
                                </small>
                            </div>
                            <span class="badge text-bg-{{ $announcement->is_active ? 'success' : 'secondary' }}">
                                {{ $announcement->is_active ? 'Tayang' : 'Nonaktif' }}
                            </span>
                        </div>
                        <p class="mt-2 mb-2" style="white-space:pre-line">{{ $announcement->body }}</p>

                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#edit-{{ $announcement->id }}">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" class="d-inline"
                              onsubmit="return confirm('Hapus pengumuman ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Hapus</button>
                        </form>

                        <div class="collapse mt-3" id="edit-{{ $announcement->id }}">
                            <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" class="border-top pt-3">
                                @csrf
                                @method('PUT')
                                <div class="mb-2">
                                    <input type="text" name="title" value="{{ $announcement->title }}" class="form-control" maxlength="150" required>
                                </div>
                                <div class="mb-2">
                                    <textarea name="body" rows="3" class="form-control" maxlength="2000" required>{{ $announcement->body }}</textarea>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="active-{{ $announcement->id }}" value="1" @checked($announcement->is_active)>
                                    <label class="form-check-label" for="active-{{ $announcement->id }}">Tampilkan ke pengguna</label>
                                </div>
                                <button class="btn btn-sm btn-primary">Simpan perubahan</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card"><div class="card-body text-center text-muted py-5">Belum ada pengumuman.</div></div>
            @endforelse
        </div>
    </div>
@endsection
