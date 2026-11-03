<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planned_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type', 30);
            $table->date('planned_date')->index();
            $table->decimal('amount', 12, 2)->nullable();
            $table->foreignId('goal_id')->nullable()->constrained('goals')->restrictOnDelete();
            $table->string('status', 20)->default('planned')->index();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planned_events');
    }
};