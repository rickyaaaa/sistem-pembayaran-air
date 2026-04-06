<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('bills')->cascadeOnDelete();
            $table->foreignId('resident_id')->constrained('residents')->cascadeOnDelete();
            $table->date('payment_date');
            $table->decimal('amount_paid', 12, 2);
            $table->string('proof_file');
            $table->string('payer_name')->nullable();
            $table->string('payer_phone')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
