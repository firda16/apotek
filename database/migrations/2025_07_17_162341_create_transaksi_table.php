<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
{
    Schema::create('transaksi', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('invoice', 50)->unique();
        $table->string('customer_name')->nullable();
        $table->string('customer_phone', 50)->nullable();
        $table->enum('payment_method', ['Cash', 'Transfer', 'QRIS']);
        $table->decimal('discount', 10, 2)->default(0.00);
        $table->decimal('total', 15, 2);
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('transaksi');
}
};
