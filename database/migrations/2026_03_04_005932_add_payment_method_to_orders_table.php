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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['saldo', 'qr', 'gopay'])->default('saldo')->after('status');
            $table->string('payment_code', 64)->nullable()->after('payment_method');
        });

        // Extend status enum to include 'menunggu_pembayaran'
        \DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('menunggu_pembayaran','pending','diproses','siap','selesai') DEFAULT 'pending'");
    }

    public function down(): void
    {
        \DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','diproses','siap','selesai') DEFAULT 'pending'");

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_code']);
        });
    }
};
