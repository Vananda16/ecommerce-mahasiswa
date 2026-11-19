<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    public const PENDING = 'pending';
    public const ACCEPTED = 'accepted';
    public const PROCESSING = 'processing';
    public const SHIPPED = 'shipped';
    public const COMPLETED = 'completed';
    public const REJECTED = 'rejected';
    public const CANCELLED = 'cancelled';

    public const STATUSES = [
        self::PENDING => ['label' => 'Menunggu konfirmasi', 'color' => 'warning'],
        self::ACCEPTED => ['label' => 'Diterima', 'color' => 'info'],
        self::PROCESSING => ['label' => 'Diproses', 'color' => 'primary'],
        self::SHIPPED => ['label' => 'Dikirim', 'color' => 'secondary'],
        self::COMPLETED => ['label' => 'Selesai', 'color' => 'success'],
        self::REJECTED => ['label' => 'Ditolak', 'color' => 'danger'],
        self::CANCELLED => ['label' => 'Dibatalkan', 'color' => 'dark'],
    ];

    /** Alur status yang boleh dilanjutkan penjual setelah pesanan diterima. */
    public const NEXT = [
        self::ACCEPTED => self::PROCESSING,
        self::PROCESSING => self::SHIPPED,
        self::SHIPPED => self::COMPLETED,
    ];

    protected $fillable = [
        'code', 'buyer_id', 'store_id', 'total', 'status',
        'shipping_address', 'note', 'rejection_reason', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => self::STATUSES[$this->status]['label'] ?? $this->status);
    }

    protected function statusColor(): Attribute
    {
        return Attribute::get(fn () => self::STATUSES[$this->status]['color'] ?? 'secondary');
    }

    /** Status berikutnya dalam alur penjual (null bila tidak ada). */
    protected function nextStatus(): Attribute
    {
        return Attribute::get(fn () => self::NEXT[$this->status] ?? null);
    }

    public function isClosed(): bool
    {
        return in_array($this->status, [self::REJECTED, self::CANCELLED], true);
    }

    /** Kembalikan stok produk (dipakai saat pesanan ditolak / dibatalkan). */
    public function restock(): void
    {
        $this->loadMissing('items.product');

        foreach ($this->items as $item) {
            $item->product?->increment('stock', $item->quantity);
        }
    }

    public static function generateCode(): string
    {
        do {
            $code = 'ORD-'.now()->format('ymd').'-'.strtoupper(\Illuminate\Support\Str::random(5));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
