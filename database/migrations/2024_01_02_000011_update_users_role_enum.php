<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite doesn't support changing ENUMs easily via change()
        // But since 'role' in users table was originally enum(['admin', 'resident'])
        // and we want to add 'pengurus' and remove/deprecate 'resident' (if logic changed)
        // the safest way for SQLite is to change the column to string.
        
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'resident'])->default('resident')->change();
        });
    }
};
