<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_WITHDRAWAL = 'withdrawal';
    public const TYPE_GOAL_CONTRIBUTION = 'goal_contribution';
    public const TYPE_GOAL_RELEASE = 'goal_release';

    public const TYPES = [
        self::TYPE_DEPOSIT => 'Deposit',
        self::TYPE_WITHDRAWAL => 'Withdrawal',
        self::TYPE_GOAL_CONTRIBUTION => 'Goal Contribution',
        self::TYPE_GOAL_RELEASE => 'Goal Release',
    ];

    protected $fillable = [
        'type',
        'amount',
        'transaction_date',
        'goal_id',
        'description',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class)->withTrashed();
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Ungkapan SQL: baki wang dalam goal = contribution - release.
     */
    public static function goalBalanceSql(): string
    {
        return "COALESCE(SUM(CASE "
            . "WHEN type = '" . self::TYPE_GOAL_CONTRIBUTION . "' THEN amount "
            . "WHEN type = '" . self::TYPE_GOAL_RELEASE . "' THEN -amount "
            . "ELSE 0 END), 0)";
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}