<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const UNPAID = 'unpaid';
    public const WAITING = 'waiting';
    public const PAID = 'paid';
    public const REJECTED = 'rejected';

    public const STATUSES = [
        self::UNPAID => ['label' => 'Belum dibayar', 'color' => 'secondary'],
        self::WAITING => ['label' => 'Menunggu verifikasi', 'color' => 'warning'],
        self::PAID => ['label' => 'Lunas', 'color' => 'success'],
        self::REJECTED => ['label' => 'Bukti ditolak', 'color' => 'danger'],
    ];

    public const METHODS = [
        'transfer' => 'Transfer Bank',
        'ewallet' => 'E-Wallet (DANA / OVO / GoPay)',
        'cod' => 'Bayar di Tempat (COD)',
    ];

    protected $fillable = ['order_id', 'method', 'status', 'proof', 'note', 'paid_at'];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => self::STATUSES[$this->status]['label'] ?? $this->status);
    }

    protected function statusColor(): Attribute
    {
        return Attribute::get(fn () => self::STATUSES[$this->status]['color'] ?? 'secondary');
    }

    protected function methodLabel(): Attribute
    {
        return Attribute::get(fn () => self::METHODS[$this->method] ?? $this->method);
    }

    protected function proofUrl(): Attribute
    {
        return Attribute::get(fn () => $this->proof ? asset('storage/'.$this->proof) : null);
    }

    public function isCod(): bool
    {
        return $this->method === 'cod';
    }
}
