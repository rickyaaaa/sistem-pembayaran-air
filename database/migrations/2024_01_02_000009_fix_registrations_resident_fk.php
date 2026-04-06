<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Drop the old cascadeOnDelete constraint
            $table->dropForeign(['resident_id']);

            // Re-add with nullOnDelete — data pemasukan tetap ada walau resident dihapus
            $table->foreign('resident_id')
                ->references('id')
                ->on('residents')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropForeign(['resident_id']);
            $table->foreignId('resident_id')->constrained('residents')->cascadeOnDelete();
        });
    }
};
