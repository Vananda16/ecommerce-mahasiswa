<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    public const OPEN = 'open';
    public const IN_REVIEW = 'in_review';
    public const RESOLVED = 'resolved';
    public const DISMISSED = 'dismissed';

    public const STATUSES = [
        self::OPEN => ['label' => 'Baru', 'color' => 'danger'],
        self::IN_REVIEW => ['label' => 'Ditinjau', 'color' => 'warning'],
        self::RESOLVED => ['label' => 'Selesai', 'color' => 'success'],
        self::DISMISSED => ['label' => 'Ditolak', 'color' => 'secondary'],
    ];

    protected $fillable = [
        'reporter_id', 'reported_user_id', 'product_id', 'subject', 'description',
        'status', 'admin_note', 'handled_by',
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => self::STATUSES[$this->status]['label'] ?? $this->status);
    }

    protected function statusColor(): Attribute
    {
        return Attribute::get(fn () => self::STATUSES[$this->status]['color'] ?? 'secondary');
    }
}
