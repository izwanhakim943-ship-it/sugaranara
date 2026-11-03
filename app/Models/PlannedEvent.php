<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlannedEvent extends Model
{
    public const TYPE_SALARY = 'salary';
    public const TYPE_PLANNED_DEPOSIT = 'planned_deposit';
    public const TYPE_REMINDER = 'reminder';

    public const STATUS_PLANNED = 'planned';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_SKIPPED = 'skipped';

    protected $fillable = [
        'title',
        'type',
        'planned_date',
        'amount',
        'goal_id',
        'status',
        'transaction_id',
        'notes',
    ];

    protected $attributes = [
        'status' => self::STATUS_PLANNED,
    ];

    protected function casts(): array
    {
        return [
            'planned_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class)->withTrashed();
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}