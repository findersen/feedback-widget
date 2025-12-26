<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Builder;

class Ticket extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    use HasFactory;
    use InteractsWithMedia;

    public const STATUS_NEW = 'new';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DONE = 'done';

    protected $fillable = [
        'customer_id',
        'subject',
        'message',
        'status',
        'manager_replied_at',
    ];

    protected $casts = [
        'manager_replied_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeForToday(Builder $query): Builder
    {
        $start = now()->startOfDay();
        $end = now()->endOfDay();

        return $query->whereBetween('created_at', [$start, $end]);
    }

    public function scopeForThisWeek(Builder $query): Builder
    {
        $start = now()->startOfWeek();
        $end = now()->endOfWeek();

        return $query->whereBetween('created_at', [$start, $end]);
    }

    public function scopeForThisMonth(Builder $query): Builder
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        return $query->whereBetween('created_at', [$start, $end]);
    }
}
