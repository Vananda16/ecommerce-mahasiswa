<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', config('app.name')) · {{ config('app.name') }}</title>

{{-- Template: Bootstrap 5 + Bootstrap Icons via CDN (tidak perlu npm / Vite) --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
    :root {
        --brand: #4f46e5;
        --brand-dark: #4338ca;
        --bs-primary: #4f46e5;
        --bs-primary-rgb: 79, 70, 229;
        --bs-link-color: #4f46e5;
        --bs-link-color-rgb: 79, 70, 229;
        --bs-link-hover-color: #4338ca;
        --bs-link-hover-color-rgb: 67, 56, 202;
    }
    body { background: #f5f6fa; }
    .btn-primary {
        --bs-btn-bg: var(--brand); --bs-btn-border-color: var(--brand);
        --bs-btn-hover-bg: var(--brand-dark); --bs-btn-hover-border-color: var(--brand-dark);
        --bs-btn-active-bg: #3730a3; --bs-btn-active-border-color: #3730a3;
        --bs-btn-disabled-bg: var(--brand); --bs-btn-disabled-border-color: var(--brand);
    }
    .btn-outline-primary {
        --bs-btn-color: var(--brand); --bs-btn-border-color: var(--brand);
        --bs-btn-hover-bg: var(--brand); --bs-btn-hover-border-color: var(--brand);
        --bs-btn-active-bg: var(--brand); --bs-btn-active-border-color: var(--brand);
    }
    .card { border: 0; box-shadow: 0 1px 3px rgba(16, 24, 40, .08); border-radius: .75rem; }
    .card-header { background: transparent; font-weight: 600; }
    .navbar-brand { letter-spacing: -.02em; }
    .sidebar { width: 250px; background: #fff; border-right: 1px solid #e5e7eb; }
    @media (min-width: 992px) {
        .sidebar { flex: 0 0 250px; min-height: calc(100vh - 57px); position: sticky; top: 57px; align-self: flex-start; height: calc(100vh - 57px); overflow-y: auto; }
    }
    .sidebar .nav-link { color: #374151; border-radius: .5rem; padding: .55rem .85rem; display: flex; align-items: center; gap: .6rem; }
    .sidebar .nav-link:hover { background: #f3f4f6; }
    .sidebar .nav-link.active { background: rgba(79, 70, 229, .1); color: var(--brand); font-weight: 600; }
    .sidebar .nav-heading { font-size: .7rem; text-transform: uppercase; letter-spacing: .06em; color: #9ca3af; padding: .9rem .85rem .3rem; }
    .product-card { transition: transform .15s, box-shadow .15s; }
    .product-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(16, 24, 40, .12); }
    .product-img { aspect-ratio: 4 / 3; object-fit: cover; width: 100%; background: #eef0f6; border-radius: .75rem .75rem 0 0; }
    .product-img-placeholder { aspect-ratio: 4 / 3; display: flex; align-items: center; justify-content: center; background: #eef0f6; color: #a5adc2; font-size: 2.5rem; border-radius: .75rem .75rem 0 0; }
    .thumb { width: 48px; height: 48px; object-fit: cover; border-radius: .5rem; background: #eef0f6; }
    .thumb-placeholder { width: 48px; height: 48px; border-radius: .5rem; background: #eef0f6; color: #a5adc2; display: inline-flex; align-items: center; justify-content: center; }
    .avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--brand); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; font-size: .8rem; }
    .stat-icon { width: 44px; height: 44px; border-radius: .75rem; display: inline-flex; align-items: center; justify-content: center; font-size: 1.3rem; }
    .chat-box { max-height: 60vh; overflow-y: auto; background: #f8f9fc; }
    .bubble { max-width: 75%; padding: .5rem .85rem; border-radius: 1rem; white-space: pre-wrap; word-break: break-word; }
    .bubble.me { background: var(--brand); color: #fff; border-bottom-right-radius: .25rem; }
    .bubble.them { background: #fff; border: 1px solid #e5e7eb; border-bottom-left-radius: .25rem; }
    .table > :not(caption) > * > * { padding-top: .75rem; padding-bottom: .75rem; }
    @media print {
        .navbar, .sidebar, .no-print, .alert { display: none !important; }
        body { background: #fff; }
        .card { box-shadow: none; border: 1px solid #ddd; }
    }
</style>
