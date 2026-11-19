@extends('layouts.app')

@php $isEdit = $product->exists; @endphp

@section('title', $isEdit ? 'Edit Produk' : 'Tambah Produk')

@section('content')
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('seller.products.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
        <h3 class="fw-bold mb-0">{{ $isEdit ? 'Edit Produk' : 'Tambah Produk' }}</h3>
    </div>

    @if($isEdit && $product->status === 'rejected')
        <div class="alert alert-danger">
            Produk ditolak admin: <strong>{{ $product->rejection_reason }}</strong>.
            Perbaiki lalu simpan untuk mengajukan ulang.
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST"
                          action="{{ $isEdit ? route('seller.products.update', $product) : route('seller.products.store') }}"
                          enctype="multipart/form-data">
                        @csrf
                        @if($isEdit) @method('PUT') @endif

                        <x-input name="name" label="Nama produk / jasa" :value="$product->name" :required="true" />

                        <div class="row">
                            <div class="col-md-6">
                                <x-select name="type" label="Jenis" :required="true">
                                    @foreach(\App\Models\Product::TYPES as $key => $label)
                                        <option value="{{ $key }}" @selected(old('type', $product->type) === $key)>{{ $label }}</option>
                                    @endforeach
                                </x-select>
                            </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Kategori</label>

                        <select name="category_id" id="category_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>

                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ (string) old('category_id', $product->category_id) === (string) $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                        </div>

                        <x-textarea name="description" label="Deskripsi" :value="$product->description" rows="5" :required="true" />

                        <div class="row">
                            <div class="col-md-6">
                                <x-input name="price" type="number" label="Harga (Rp)" :value="$product->price" :required="true" min="0" step="1" />
                            </div>
                            <div class="col-md-6">
                                <x-input name="stock" type="number" label="Stok / kuota" :value="$product->stock" :required="true" min="0" step="1"
                                         help="Untuk jasa, isi dengan kuota pesanan yang bisa Anda terima." />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto produk</label>
                            @if($product->image_url)
                                <div class="mb-2"><img src="{{ $product->image_url }}" alt="" class="rounded border" style="max-height:140px"></div>
                            @endif
                            <input type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">JPG / PNG / WEBP, maksimal 2 MB.{{ $isEdit ? ' Kosongkan bila tidak ingin mengganti foto.' : '' }}</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" type="submit">{{ $isEdit ? 'Simpan perubahan' : 'Simpan produk' }}</button>
                            <a href="{{ route('seller.products.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="alert alert-info small">
                <i class="bi bi-info-circle"></i>
                Produk baru akan diverifikasi admin terlebih dahulu sebelum tampil di katalog.
            </div>
        </div>
    </div>
@endsection
