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
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); // primary key
            $table->string('name'); // nama customer
            $table->string('phone')->nullable(); // nomor telepon
            $table->string('email')->nullable(); // email, opsional
            $table->text('address')->nullable(); // alamat            
            $table->timestamps(); // created_at dan updated_at
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
