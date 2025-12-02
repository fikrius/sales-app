<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change status from ENUM to VARCHAR to support longer status text
        DB::statement("ALTER TABLE sales MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'Belum Dibayar'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM (data may be lost)
        DB::statement("ALTER TABLE sales MODIFY COLUMN status ENUM('Belum Dibayar', 'Sudah Dibayar') NOT NULL DEFAULT 'Belum Dibayar'");
    }
};
