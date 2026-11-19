<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = ['sender_id', 'receiver_id', 'body', 'read_at'];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /** Semua pesan antara dua user (dua arah). */
    public function scopeBetween(Builder $query, int $a, int $b): Builder
    {
        return $query->where(function (Builder $outer) use ($a, $b) {
            $outer->where(fn (Builder $q) => $q->where('sender_id', $a)->where('receiver_id', $b))
                ->orWhere(fn (Builder $q) => $q->where('sender_id', $b)->where('receiver_id', $a));
        });
    }
}
