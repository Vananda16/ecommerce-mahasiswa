<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public const PENDING = 'pending';
    public const APPROVED = 'approved';
    public const REJECTED = 'rejected';

    public const STATUSES = [
        self::PENDING => ['label' => 'Menunggu verifikasi', 'color' => 'warning'],
        self::APPROVED => ['label' => 'Disetujui', 'color' => 'success'],
        self::REJECTED => ['label' => 'Ditolak', 'color' => 'danger'],
    ];

    public const TYPES = [
        'barang' => 'Barang',
        'jasa' => 'Jasa',
    ];

    protected $fillable = [
        'category_id', 'type', 'name', 'description', 'price', 'stock',
        'image', 'status', 'rejection_reason',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Produk yang tampil di katalog publik: sudah disetujui admin & penjualnya tidak diblokir. */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('status', self::APPROVED)
            ->whereHas('store.user', fn (Builder $u) => $u->where('is_blocked', false));
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->image ? asset('storage/'.$this->image) : null);
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => self::STATUSES[$this->status]['label'] ?? $this->status);
    }

    protected function statusColor(): Attribute
    {
        return Attribute::get(fn () => self::STATUSES[$this->status]['color'] ?? 'secondary');
    }

    protected function typeLabel(): Attribute
    {
        return Attribute::get(fn () => self::TYPES[$this->type] ?? $this->type);
    }
}
