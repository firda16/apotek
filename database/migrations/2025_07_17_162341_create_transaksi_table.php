<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice', 50)->nullable(); // sebelumnya kode_transaksi
            $table->decimal('total_harga', 10, 2);
            $table->string('nama_pelanggan', 255)->nullable();
            $table->string('nomor_telepon', 50)->nullable();
            $table->string('metode_pembayaran', 100)->nullable();
            $table->integer('diskon')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
