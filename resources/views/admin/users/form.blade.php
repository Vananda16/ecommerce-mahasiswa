@extends('layouts.app')

@php $isEdit = $user->exists; @endphp

@section('title', $isEdit ? 'Edit Mahasiswa' : 'Tambah Mahasiswa')

@section('content')
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
        <h3 class="fw-bold mb-0">{{ $isEdit ? 'Edit Mahasiswa' : 'Tambah Mahasiswa' }}</h3>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}">
                        @csrf
                        @if($isEdit) @method('PUT') @endif

                        <x-input name="name" label="Nama lengkap" :value="$user->name" :required="true" />
                        <x-input name="email" type="email" label="Email" :value="$user->email" :required="true" />

                        <div class="row">
                            <div class="col-md-6"><x-input name="nim" label="NIM" :value="$user->nim" /></div>
                            <div class="col-md-6"><x-input name="phone" label="No. HP" :value="$user->phone" /></div>
                        </div>

                        <x-textarea name="address" label="Alamat" :value="$user->address" rows="2" />

                        <x-select name="role" label="Peran" :required="true">
                            <option value="buyer" @selected(old('role', $user->role) === 'buyer')>Pembeli</option>
                            <option value="seller" @selected(old('role', $user->role) === 'seller')>Penjual</option>
                        </x-select>

                        <x-input name="password" type="password" label="Password"
                                 :required="! $isEdit"
                                 :help="$isEdit ? 'Kosongkan bila tidak ingin mengubah password.' : 'Minimal 8 karakter.'" />

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" type="submit">Simpan</button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
