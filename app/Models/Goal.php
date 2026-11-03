<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Goal extends Model
{
    use SoftDeletes;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_ARCHIVED = 'archived';

    public const CATEGORIES = [
        'Travel', 'Wedding', 'Emergency', 'Education',
        'Home', 'Vehicle', 'Gadget', 'Other',
    ];

    // Warna dari palet Sugaranara
    public const COLOURS = [
        '#D9663A', // terracotta
        '#FF7A6B', // coral
        '#F2B63D', // mustard
        '#4FA6A0', // lagoon
        '#1F4D3A', // forest
        '#A9C4A0', // sage
        '#BFE87A', // lime
    ];

    protected $fillable = [
        'name',
        'description',
        'target_amount',
        'target_date',
        'image_path',
        'category',
        'colour',
        'status',
    ];

    protected $attributes = [
        'status' => self::STATUS_ACTIVE,
        'category' => 'Other',
        'colour' => '#D9663A',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'target_date' => 'date',
        ];
    }

    /* ---------- Hubungan ---------- */

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function plannedEvents(): HasMany
    {
        return $this->hasMany(PlannedEvent::class);
    }

    /* ---------- Scopes ---------- */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Tambah lajur `saved_total` (contribution - release) dalam satu query,
     * supaya senarai goal tidak perlu query berulang untuk setiap goal.
     */
    public function scopeWithSavedAmount(Builder $query): Builder
    {
        return $query->addSelect([
            'saved_total' => Transaction::query()
                ->selectRaw(Transaction::goalBalanceSql())
                ->whereColumn('goal_id', 'goals.id'),
        ]);
    }

    /* ---------- Accessors (dikira, bukan disimpan) ---------- */

    protected function savedAmount(): Attribute
    {
        return Attribute::make(get: function () {
            if (array_key_exists('saved_total', $this->attributes)) {
                return round((float) $this->attributes['saved_total'], 2);
            }

            $total = $this->transactions()
                ->selectRaw(Transaction::goalBalanceSql() . ' as total')
                ->value('total');

            return round((float) $total, 2);
        });
    }

    protected function remainingAmount(): Attribute
    {
        return Attribute::make(get: fn () => max(0, round((float) $this->target_amount - $this->saved_amount, 2)));
    }

    protected function progressPercent(): Attribute
    {
        return Attribute::make(get: function () {
            $target = (float) $this->target_amount;

            if ($target <= 0) {
                return 0;
            }

            return (int) min(100, floor($this->saved_amount / $target * 100));
        });
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(get: fn () => $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : null);
    }
}