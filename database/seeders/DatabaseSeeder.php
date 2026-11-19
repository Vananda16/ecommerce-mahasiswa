<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Akun demo (semua password: "password"):
     *   admin@marketplace.test   -> Admin
     *   penjual@marketplace.test -> Penjual
     *   pembeli@marketplace.test -> Pembeli
     */
    public function run(): void
    {
        // --- Admin (tidak bisa mendaftar sendiri, hanya dibuat lewat seeder) ---
        $admin = new User([
            'name' => 'Administrator',
            'email' => 'admin@marketplace.test',
            'password' => 'password',
        ]);
        $admin->role = User::ADMIN;
        $admin->email_verified_at = now();
        $admin->save();

        // --- Penjual demo ---
        $seller = new User([
            'name' => 'Budi Santoso',
            'email' => 'penjual@marketplace.test',
            'password' => 'password',
            'nim' => '2457301001',
            'phone' => '081234567890',
            'address' => 'Kos Melati, Jl. Umban Sari, Pekanbaru',
        ]);
        $seller->role = User::SELLER;
        $seller->email_verified_at = now();
        $seller->save();

        $store = $seller->store()->create([
            'name' => 'Toko Budi',
            'description' => 'Menjual buku, perlengkapan kuliah, dan jasa ketik/desain untuk sesama mahasiswa.',
            'address' => 'Area kampus & sekitar Umban Sari (COD)',
            'payment_info' => 'DANA 0812-3456-7890 a.n. Budi Santoso',
        ]);

        // --- Pembeli demo ---
        $buyer = new User([
            'name' => 'Siti Aisyah',
            'email' => 'pembeli@marketplace.test',
            'password' => 'password',
            'nim' => '2457301002',
            'phone' => '082345678901',
            'address' => 'Asrama Putri Blok C No. 5',
        ]);
        $buyer->role = User::BUYER;
        $buyer->email_verified_at = now();
        $buyer->save();

        // --- Kategori ---
        $categories = collect([
            'Buku & Alat Tulis', 'Elektronik', 'Makanan & Minuman',
            'Fashion', 'Jasa Akademik', 'Jasa Desain & Kreatif', 'Perlengkapan Kos',
        ])->mapWithKeys(fn (string $name) => [
            $name => Category::create(['name' => $name, 'slug' => Str::slug($name)]),
        ]);

        // --- Produk contoh (status approved supaya langsung tampil di katalog) ---
        $products = [
            ['Buku Algoritma & Struktur Data (bekas)', 'Buku & Alat Tulis', 'barang', 'Buku Algoritma & Struktur Data edisi terbaru, kondisi 90%, ada sedikit coretan di bab 3.', 55000, 3],
            ['Kalkulator Scientific Casio fx-991', 'Elektronik', 'barang', 'Kalkulator scientific masih mulus, baterai baru diganti, lengkap dengan cover.', 120000, 2],
            ['Nasi Ayam Geprek + Es Teh', 'Makanan & Minuman', 'barang', 'Ayam geprek level pedas bisa dipilih. Order sebelum jam 10.00 untuk makan siang.', 15000, 30],
            ['Jasa Ketik & Format Laporan', 'Jasa Akademik', 'jasa', 'Ketik ulang dan rapikan format laporan/makalah sesuai pedoman kampus. Estimasi 1–2 hari.', 25000, 10],
            ['Jasa Desain Poster Kegiatan', 'Jasa Desain & Kreatif', 'jasa', 'Desain poster/banner kegiatan organisasi. Revisi 2x, file PNG + PDF.', 75000, 5],
            ['Rak Buku Lipat Portable', 'Perlengkapan Kos', 'barang', 'Rak lipat 3 tingkat, cocok untuk kamar kos. Tanpa perlu dirakit.', 85000, 4],
        ];

        foreach ($products as [$name, $category, $type, $description, $price, $stock]) {
            $store->products()->create([
                'category_id' => $categories[$category]->id,
                'type' => $type,
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'stock' => $stock,
                'status' => Product::APPROVED,
            ]);
        }

        // Satu produk yang masih menunggu verifikasi admin
        $store->products()->create([
            'category_id' => $categories['Fashion']->id,
            'type' => 'barang',
            'name' => 'Jaket Almamater Ukuran L',
            'description' => 'Jaket almamater bekas, ukuran L, kondisi bagus.',
            'price' => 90000,
            'stock' => 1,
            'status' => Product::PENDING,
        ]);

        Announcement::create([
            'title' => 'Selamat datang di Marketplace Mahasiswa!',
            'body' => 'Dilarang menjual barang ilegal atau melanggar aturan kampus. Laporkan produk/akun mencurigakan lewat menu Pengaduan.',
            'is_active' => true,
            'created_by' => $admin->id,
        ]);
    }
}
