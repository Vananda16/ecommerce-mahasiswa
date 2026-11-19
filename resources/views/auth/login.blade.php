@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <h4 class="fw-bold mb-1">Masuk</h4>
    <p class="text-muted">Masuk sebagai penjual, pembeli, atau admin.</p>

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <x-input name="email" type="email" label="Email" :required="true" autofocus autocomplete="username" />
        <x-input name="password" type="password" label="Password" :required="true" autocomplete="current-password" />

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
            <label class="form-check-label" for="remember">Ingat saya</label>
        </div>

        <button class="btn btn-primary w-100" type="submit">Masuk</button>
    </form>

    <hr>
    <p class="text-center mb-0">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
@endsection
