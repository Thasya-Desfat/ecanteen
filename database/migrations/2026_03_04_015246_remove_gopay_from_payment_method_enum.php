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
        \DB::table('orders')->where('payment_method', 'gopay')->update(['payment_method' => 'cash']);
        \DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('saldo','cash') NOT NULL DEFAULT 'saldo'");
    }

    public function down(): void
    {
        \DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('saldo','cash','gopay') NOT NULL DEFAULT 'saldo'");
    }
};
