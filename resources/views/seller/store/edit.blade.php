@extends('layouts.app')

@section('title', 'Profil Toko')

@section('content')
    <h3 class="fw-bold mb-4">Profil Toko</h3>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('seller.store.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="d-flex align-items-center gap-3 mb-4">
                            @if($store->logo_url)
                                <img src="{{ $store->logo_url }}" alt="Logo toko" class="rounded-circle" style="width:72px;height:72px;object-fit:cover">
                            @else
                                <span class="avatar" style="width:72px;height:72px;font-size:1.5rem"><i class="bi bi-shop"></i></span>
                            @endif
                            <div class="flex-grow-1">
                                <label class="form-label small mb-1">Logo toko</label>
                                <input type="file" name="logo" accept="image/*" class="form-control @error('logo') is-invalid @enderror">
                                @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <x-input name="name" label="Nama toko" :value="$store->name" :required="true" />
                        <x-textarea name="description" label="Deskripsi toko" :value="$store->description" rows="4" />
                        <x-input name="address" label="Lokasi (kos / asrama / area COD)" :value="$store->address" />
                        <x-input name="payment_info" label="Info pembayaran"
                                 :value="$store->payment_info"
                                 help="Ditampilkan ke pembeli. Contoh: DANA 0812-xxxx a.n. Nama, atau BRI 1234-56-789 a.n. Nama." />

                        <button class="btn btn-primary" type="submit">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
