@extends('layouts.guest')

@section('title', 'Daftar')

@section('content')
    <h4 class="fw-bold mb-1">Buat akun</h4>
    <p class="text-muted">Daftar sebagai pembeli atau penjual.</p>

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label d-block">Daftar sebagai <span class="text-danger">*</span></label>
            <div class="row g-2">
                <div class="col-6">
                    <input type="radio" class="btn-check" name="role" id="role-buyer" value="buyer"
                           @checked(old('role', 'buyer') === 'buyer')>
                    <label class="btn btn-outline-primary w-100 py-3" for="role-buyer">
                        <i class="bi bi-bag d-block fs-4"></i> Pembeli
                    </label>
                </div>
                <div class="col-6">
                    <input type="radio" class="btn-check" name="role" id="role-seller" value="seller"
                           @checked(old('role') === 'seller')>
                    <label class="btn btn-outline-primary w-100 py-3" for="role-seller">
                        <i class="bi bi-shop d-block fs-4"></i> Penjual
                    </label>
                </div>
            </div>
            @error('role')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <x-input name="name" label="Nama lengkap" :required="true" autocomplete="name" />
        <x-input name="email" type="email" label="Email" :required="true" autocomplete="username" />

        <div class="row">
            <div class="col-md-6"><x-input name="nim" label="NIM" /></div>
            <div class="col-md-6"><x-input name="phone" label="No. HP / WhatsApp" /></div>
        </div>

        <x-input name="password" type="password" label="Password" :required="true" help="Minimal 8 karakter." autocomplete="new-password" />
        <x-input name="password_confirmation" type="password" label="Ulangi password" :required="true" autocomplete="new-password" />

        <button class="btn btn-primary w-100" type="submit">Daftar</button>
    </form>

    <hr>
    <p class="text-center mb-0">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
@endsection
