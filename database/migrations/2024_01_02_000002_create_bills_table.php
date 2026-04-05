<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained('residents')->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['unpaid', 'pending', 'paid'])->default('unpaid');
            $table->timestamps();

            $table->unique(['resident_id', 'month', 'year']);
            $table->index(['month', 'year']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
