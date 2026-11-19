@extends('layouts.app')

@section('title', 'Verifikasi Produk')

@section('content')
    <h3 class="fw-bold mb-3">Produk</h3>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama produk…">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Semua status</option>
                @foreach(\App\Models\Product::STATUSES as $key => $meta)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $meta['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">Semua kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-outline-primary">Filter</button></div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Produk</th><th>Penjual</th><th>Kategori</th><th class="text-end">Harga</th><th class="text-center">Stok</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" class="thumb" alt="">
                                    @else
                                        <span class="thumb-placeholder"><i class="bi bi-image"></i></span>
                                    @endif
                                    <div>
                                        <a href="{{ route('products.show', $product) }}" class="text-decoration-none fw-semibold" target="_blank">{{ \Illuminate\Support\Str::limit($product->name, 36) }}</a>
                                        <div class="small text-muted">{{ $product->type_label }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $product->store->name }}
                                <div class="small text-muted">{{ $product->store->user->name }}</div>
                            </td>
                            <td>{{ $product->category?->name ?? '-' }}</td>
                            <td class="text-end">@rupiah($product->price)</td>
                            <td class="text-center">{{ $product->stock }}</td>
                            <td>
                                <span class="badge text-bg-{{ $product->status_color }}">{{ $product->status_label }}</span>
                                @if($product->rejection_reason)<div class="small text-danger">{{ $product->rejection_reason }}</div>@endif
                            </td>
                            <td class="text-end text-nowrap">
                                @if($product->status !== 'approved')
                                    <form method="POST" action="{{ route('admin.products.approve', $product) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success" title="Setujui"><i class="bi bi-check-lg"></i></button>
                                    </form>
                                @endif
                                @if($product->status !== 'rejected')
                                    <button type="button" class="btn btn-sm btn-outline-warning" title="Tolak"
                                            data-bs-toggle="modal" data-bs-target="#rejectModal"
                                            data-action="{{ route('admin.products.reject', $product) }}" data-name="{{ $product->name }}">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                @endif
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline"
                                      onsubmit="return confirm('Hapus produk ini karena melanggar aturan?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-5">Tidak ada produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $products->links() }}</div>

    {{-- Modal tolak produk --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content" id="rejectForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tolak produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2 fw-semibold" id="rejectName"></p>
                    <label class="form-label">Alasan penolakan <span class="text-danger">*</span></label>
                    <input type="text" name="rejection_reason" class="form-control" maxlength="255" required>
                    <div class="form-text">Alasan ini akan terlihat oleh penjual agar bisa memperbaiki produk.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-warning">Tolak produk</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('rejectModal').addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            document.getElementById('rejectForm').action = btn.dataset.action;
            document.getElementById('rejectName').textContent = btn.dataset.name;
        });
    </script>
@endpush
