<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Catatan: kolom `role` dan `is_blocked` sengaja TIDAK mass-assignable
// supaya tidak bisa diubah lewat form biasa. Set manual di controller/seeder.
#[Fillable(['name', 'email', 'password', 'nim', 'phone', 'address'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ADMIN = 'admin';
    public const SELLER = 'seller';
    public const BUYER = 'buyer';

    public const ROLES = [
        self::ADMIN => 'Administrator',
        self::SELLER => 'Penjual',
        self::BUYER => 'Pembeli',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_blocked' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ADMIN;
    }

    public function isSeller(): bool
    {
        return $this->role === self::SELLER;
    }

    public function isBuyer(): bool
    {
        return $this->role === self::BUYER;
    }

    /** Nama route halaman utama untuk masing-masing role. */
    public function homeRoute(): string
    {
        return match ($this->role) {
            self::ADMIN => 'admin.dashboard',
            self::SELLER => 'seller.dashboard',
            default => 'home',
        };
    }

    protected function roleLabel(): Attribute
    {
        return Attribute::get(fn () => self::ROLES[$this->role] ?? $this->role);
    }

    protected function initials(): Attribute
    {
        return Attribute::get(function () {
            $parts = preg_split('/\s+/', trim($this->name)) ?: [];

            return strtoupper(mb_substr($parts[0] ?? '?', 0, 1).mb_substr($parts[1] ?? '', 0, 1));
        });
    }

    public function store(): HasOne
    {
        return $this->hasOne(Store::class);
    }

    /** Pesanan milik pembeli. */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /** Pengaduan yang dibuat user ini. */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }
}
