<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->index('resident_id');
            $table->index('bill_id');
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['resident_id']);
            $table->dropIndex(['bill_id']);
            $table->dropIndex(['payment_date']);
        });
    }
};
