@extends('layouts.app')

@section('title', 'Data Mahasiswa')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h3 class="fw-bold mb-0">Data Mahasiswa</h3>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="bi bi-person-plus"></i> Tambah Mahasiswa</a>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama / email / NIM…">
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select">
                <option value="">Semua peran</option>
                <option value="seller" @selected(request('role') === 'seller')>Penjual</option>
                <option value="buyer" @selected(request('role') === 'buyer')>Pembeli</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Semua status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="blocked" @selected(request('status') === 'blocked')>Diblokir</option>
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-outline-primary">Filter</button></div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Mahasiswa</th><th>NIM</th><th>Peran</th><th>Status</th><th>Terdaftar</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar">{{ $user->initials }}</span>
                                    <div>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                        <div class="small text-muted">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->nim ?: '-' }}</td>
                            <td>
                                {{ $user->role_label }}
                                @if($user->isSeller() && $user->store)
                                    <div class="small text-muted">{{ $user->store->name }}</div>
                                @endif
                            </td>
                            <td>
                                @if($user->is_blocked)
                                    <span class="badge text-bg-danger">Diblokir</span>
                                    <div class="small text-muted">{{ $user->blocked_reason }}</div>
                                @else
                                    <span class="badge text-bg-success">Aktif</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $user->created_at->translatedFormat('d M Y') }}</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>

                                @if($user->is_blocked)
                                    <form method="POST" action="{{ route('admin.users.unblock', $user) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success" title="Buka blokir"><i class="bi bi-unlock"></i></button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-warning" title="Blokir"
                                            data-bs-toggle="modal" data-bs-target="#blockModal"
                                            data-action="{{ route('admin.users.block', $user) }}" data-name="{{ $user->name }}">
                                        <i class="bi bi-slash-circle"></i>
                                    </button>
                                @endif

                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline"
                                      onsubmit="return confirm('Hapus akun {{ $user->name }}? Semua data terkait (produk, pesanan, ulasan) ikut terhapus. Disarankan memblokir saja.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $users->links() }}</div>

    {{-- Modal blokir --}}
    <div class="modal fade" id="blockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content" id="blockForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Blokir akun <span id="blockName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Alasan pemblokiran <span class="text-danger">*</span></label>
                    <input type="text" name="blocked_reason" class="form-control" maxlength="255" required>
                    <div class="form-text">Pengguna akan langsung dikeluarkan, dan produk penjual disembunyikan dari katalog.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-warning">Blokir</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('blockModal').addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            document.getElementById('blockForm').action = btn.dataset.action;
            document.getElementById('blockName').textContent = btn.dataset.name;
        });
    </script>
@endpush
