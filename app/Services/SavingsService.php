<?php

namespace App\Services;

use App\Exceptions\InsufficientFundsException;
use App\Models\Goal;
use App\Models\Setting;
use App\Models\Transaction;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SavingsService
{
    /* =========================================================
       PENGIRAAN BAKI (semua dikira daripada transaksi)
       ========================================================= */

    /**
     * Available Savings = deposit + release - withdrawal - contribution
     */
    public function availableSavings(): float
    {
        return $this->centsToAmount($this->availableCents());
    }

    /**
     * Total wang yang diperuntukkan kepada semua goal = contribution - release
     */
    public function allocatedToGoals(): float
    {
        return $this->centsToAmount($this->allocatedCents());
    }

    /**
     * Total Savings = Available + Allocated
     */
    public function totalSavings(): float
    {
        return $this->centsToAmount($this->availableCents() + $this->allocatedCents());
    }

    public function summary(): array
    {
        return [
            'available'    => $this->availableSavings(),
            'allocated'    => $this->allocatedToGoals(),
            'total'        => $this->totalSavings(),
            'active_goals' => Goal::active()->count(),
        ];
    }

    /* =========================================================
       OPERASI WANG
       ========================================================= */

    public function deposit(
        float|int|string $amount,
        string|DateTimeInterface $date,
        ?string $description = null,
        ?string $notes = null,
    ): Transaction {
        $cents = $this->toCents($amount);

        return DB::transaction(function () use ($cents, $date, $description, $notes) {
            $this->lockLedger();

            return Transaction::create([
                'type'             => Transaction::TYPE_DEPOSIT,
                'amount'           => $this->centsToAmount($cents),
                'transaction_date' => $date,
                'description'      => $description,
                'notes'            => $notes,
            ]);
        });
    }

    public function withdraw(
        float|int|string $amount,
        string|DateTimeInterface $date,
        ?string $description = null,
        ?string $notes = null,
    ): Transaction {
        $cents = $this->toCents($amount);

        return DB::transaction(function () use ($cents, $date, $description, $notes) {
            $this->lockLedger();

            if ($cents > $this->availableCents()) {
                throw new InsufficientFundsException(
                    'Jumlah withdrawal melebihi Available Savings anda.'
                );
            }

            return Transaction::create([
                'type'             => Transaction::TYPE_WITHDRAWAL,
                'amount'           => $this->centsToAmount($cents),
                'transaction_date' => $date,
                'description'      => $description,
                'notes'            => $notes,
            ]);
        });
    }

    /**
     * Alih wang dari Current Savings ke dalam goal.
     */
    public function contribute(
        Goal $goal,
        float|int|string $amount,
        string|DateTimeInterface $date,
        ?string $description = null,
        ?string $notes = null,
    ): Transaction {
        $cents = $this->toCents($amount);

        return DB::transaction(function () use ($goal, $cents, $date, $description, $notes) {
            $this->lockLedger();

            if ($goal->trashed() || $goal->status === Goal::STATUS_ARCHIVED) {
                throw new InvalidArgumentException('Goal ini sudah diarkibkan atau dipadam.');
            }

            if ($cents > $this->availableCents()) {
                throw new InsufficientFundsException(
                    'Jumlah contribution melebihi Available Savings anda.'
                );
            }

            $transaction = Transaction::create([
                'type'             => Transaction::TYPE_GOAL_CONTRIBUTION,
                'amount'           => $this->centsToAmount($cents),
                'transaction_date' => $date,
                'goal_id'          => $goal->id,
                'description'      => $description,
                'notes'            => $notes,
            ]);

            $this->syncGoalStatus($goal);

            return $transaction;
        });
    }

    /**
     * Alih wang dari goal kembali ke Current Savings.
     */
    public function release(
        Goal $goal,
        float|int|string $amount,
        string|DateTimeInterface $date,
        ?string $description = null,
        ?string $notes = null,
    ): Transaction {
        $cents = $this->toCents($amount);

        return DB::transaction(function () use ($goal, $cents, $date, $description, $notes) {
            $this->lockLedger();

            if ($cents > $this->goalBalanceCents($goal->id)) {
                throw new InsufficientFundsException(
                    'Jumlah release melebihi baki dalam goal ini.'
                );
            }

            $transaction = Transaction::create([
                'type'             => Transaction::TYPE_GOAL_RELEASE,
                'amount'           => $this->centsToAmount($cents),
                'transaction_date' => $date,
                'goal_id'          => $goal->id,
                'description'      => $description,
                'notes'            => $notes,
            ]);

            $this->syncGoalStatus($goal);

            return $transaction;
        });
    }

    /* =========================================================
       FUNGSI DALAMAN
       ========================================================= */

    /**
     * Kunci "lejar" supaya dua operasi tidak berlaku serentak.
     */
    private function lockLedger(): void
    {
        Setting::firstOrCreate(['key' => 'ledger_lock'], ['value' => null]);

        Setting::where('key', 'ledger_lock')->lockForUpdate()->first();
    }

    private function availableCents(): int
    {
        $sql = "COALESCE(SUM(CASE type "
            . "WHEN '" . Transaction::TYPE_DEPOSIT . "' THEN amount "
            . "WHEN '" . Transaction::TYPE_GOAL_RELEASE . "' THEN amount "
            . "WHEN '" . Transaction::TYPE_WITHDRAWAL . "' THEN -amount "
            . "WHEN '" . Transaction::TYPE_GOAL_CONTRIBUTION . "' THEN -amount "
            . "ELSE 0 END), 0)";

        $total = Transaction::query()->selectRaw($sql . ' as total')->value('total');

        return $this->toCents($total, allowZeroOrNegative: true);
    }

    private function allocatedCents(): int
    {
        $total = Transaction::query()
            ->selectRaw(Transaction::goalBalanceSql() . ' as total')
            ->value('total');

        return $this->toCents($total, allowZeroOrNegative: true);
    }

    private function goalBalanceCents(int $goalId): int
    {
        $total = Transaction::query()
            ->where('goal_id', $goalId)
            ->selectRaw(Transaction::goalBalanceSql() . ' as total')
            ->value('total');

        return $this->toCents($total, allowZeroOrNegative: true);
    }

    /**
     * Tukar status goal: completed jika sudah cukup, active jika belum.
     */
    private function syncGoalStatus(Goal $goal): void
    {
        if ($goal->status === Goal::STATUS_ARCHIVED) {
            return;
        }

        $saved  = $this->goalBalanceCents($goal->id);
        $target = $this->toCents($goal->target_amount);

        $goal->status = $saved >= $target
            ? Goal::STATUS_COMPLETED
            : Goal::STATUS_ACTIVE;

        $goal->save();
    }

    /**
     * Tukar jumlah RM kepada sen (integer) untuk elak ralat pecahan.
     */
    private function toCents(float|int|string|null $amount, bool $allowZeroOrNegative = false): int
    {
        $cents = (int) round(((float) $amount) * 100);

        if (! $allowZeroOrNegative && $cents <= 0) {
            throw new InvalidArgumentException('Jumlah mesti lebih daripada RM0.00.');
        }

        return $cents;
    }

    private function centsToAmount(int $cents): float
    {
        return round($cents / 100, 2);
    }
}