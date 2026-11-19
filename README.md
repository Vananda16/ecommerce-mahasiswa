# Marketplace Mahasiswa (Laravel)

Marketplace khusus mahasiswa dengan 3 role: **Penjual**, **Pembeli**, **Admin**.
Template: Blade + Bootstrap (via CDN, tanpa build frontend). Database default: SQLite.

## Cara menjalankan

Syarat: PHP 8.3+, Composer.

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite        # (Windows: type nul > database\database.sqlite)
php artisan migrate --seed
php artisan storage:link              # supaya foto produk/toko tampil
php artisan serve
```

Buka http://127.0.0.1:8000

Untuk MySQL: ubah `DB_CONNECTION=mysql` dan isi `DB_*` di `.env`, lalu `php artisan migrate --seed`.

## Akun demo (password: `password`)

| Role   | Email                      |
|--------|----------------------------|
| Admin  | admin@marketplace.test     |
| Penjual| penjual@marketplace.test   |
| Pembeli| pembeli@marketplace.test   |

Penjual dan pembeli bisa registrasi sendiri; admin hanya dibuat lewat seeder / panel admin.

## Fitur per role

**Penjual** (`/seller`): profil toko, CRUD produk + foto + harga + stok, pesanan masuk (terima/tolak, status diproses → dikirim → selesai, konfirmasi pembayaran), riwayat penjualan, ulasan & rating, chat dengan pembeli.

**Pembeli**: profil, daftar produk, cari nama, filter kategori & harga, detail produk, keranjang, pemesanan, konfirmasi pembayaran, status & riwayat pesanan, ulasan & rating, chat dengan penjual, pengaduan.

**Admin** (`/admin`): dashboard statistik, kelola mahasiswa (penjual & pembeli), semua produk, CRUD kategori, verifikasi/tolak/hapus produk, kelola transaksi, pantau aktivitas, kelola pengaduan, blokir akun, laporan penjualan & pembelian, pengumuman sistem.
