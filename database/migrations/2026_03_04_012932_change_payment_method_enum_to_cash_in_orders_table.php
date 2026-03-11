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
        // Step 1: expand ENUM to allow both old and new value
        \DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('saldo','qr','cash','gopay') NOT NULL DEFAULT 'saldo'");
        // Step 2: migrate existing rows
        \DB::table('orders')->where('payment_method', 'qr')->update(['payment_method' => 'cash']);
        // Step 3: remove old value
        \DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('saldo','cash','gopay') NOT NULL DEFAULT 'saldo'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('saldo','qr','cash','gopay') NOT NULL DEFAULT 'saldo'");
        \DB::table('orders')->where('payment_method', 'cash')->update(['payment_method' => 'qr']);
        \DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('saldo','qr','gopay') NOT NULL DEFAULT 'saldo'");
    }
};
