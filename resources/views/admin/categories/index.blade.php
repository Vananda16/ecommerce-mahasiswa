@extends('layouts.app')

@section('title', 'Kategori Produk')

@section('content')
    <h3 class="fw-bold mb-4">Kategori Produk</h3>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Tambah kategori</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.categories.store') }}">
                        @csrf
                        <x-input name="name" label="Nama kategori" :required="true" />
                        <button class="btn btn-primary" type="submit"><i class="bi bi-plus-lg"></i> Tambah</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light"><tr><th>Nama kategori</th><th class="text-center">Produk</th><th></th></tr></thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>
                                        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="d-flex gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="name" value="{{ $category->name }}" class="form-control form-control-sm" maxlength="80" required>
                                            <button class="btn btn-sm btn-outline-primary" title="Simpan"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                    </td>
                                    <td class="text-center">{{ $category->products_count }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline"
                                              onsubmit="return confirm('Hapus kategori {{ $category->name }}? Produk di dalamnya tidak ikut terhapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada kategori.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($errors->has('name'))
                <div class="text-danger small mt-2">{{ $errors->first('name') }}</div>
            @endif
        </div>
    </div>
@endsection
