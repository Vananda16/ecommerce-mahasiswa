@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <h3 class="fw-bold mb-4">Profil Saya</h3>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">Data pribadi</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <x-input name="name" label="Nama lengkap" :value="$user->name" :required="true" />
                        <x-input name="email" type="email" label="Email" :value="$user->email" :required="true" />

                        <div class="row">
                            <div class="col-md-6"><x-input name="nim" label="NIM" :value="$user->nim" /></div>
                            <div class="col-md-6"><x-input name="phone" label="No. HP / WhatsApp" :value="$user->phone" /></div>
                        </div>

                        <x-textarea name="address" label="Alamat / lokasi (kos, asrama, dsb.)" :value="$user->address" />

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Peran: <strong>{{ $user->role_label }}</strong></span>
                            <button class="btn btn-primary" type="submit">Simpan perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">Ubah password</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')

                        <x-input name="current_password" type="password" label="Password saat ini" :required="true" autocomplete="current-password" />
                        <x-input name="password" type="password" label="Password baru" :required="true" help="Minimal 8 karakter." autocomplete="new-password" />
                        <x-input name="password_confirmation" type="password" label="Ulangi password baru" :required="true" autocomplete="new-password" />

                        <button class="btn btn-outline-primary" type="submit">Ubah password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
