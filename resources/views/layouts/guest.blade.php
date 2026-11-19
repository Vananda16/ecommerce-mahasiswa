<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="text-center mb-4">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <h3 class="fw-bold text-primary mb-1"><i class="bi bi-shop"></i> {{ config('app.name') }}</h3>
                </a>
                <p class="text-muted mb-0">Jual beli barang &amp; jasa antar mahasiswa</p>
            </div>

            @include('partials.flash')

            <div class="card">
                <div class="card-body p-4">
                    @yield('content')
                </div>
            </div>

            <p class="text-center mt-3 mb-0">
                <a href="{{ route('home') }}" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Kembali ke katalog</a>
            </p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
