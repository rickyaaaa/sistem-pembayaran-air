<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Tambah kolom kategori setelah kolom resident_id
            $table->string('category')->default('pendaftaran')->after('resident_id');
            // resident_id jadi nullable karena Kas Masuk/Sumbangan mungkin tidak spesifik per warga
            $table->unsignedBigInteger('resident_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn('category');
            $table->unsignedBigInteger('resident_id')->nullable(false)->change();
        });
    }
};
