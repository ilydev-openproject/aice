<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->decimal('hpp', 10, 2); // harga pokok ke toko
            $table->decimal('het', 10, 2); // harga eceran tertinggi
            $table->integer('isi_per_box'); // misal: 24
            $table->string('foto')->nullable();
            $table->decimal('margin', 10, 2); // harga eceran tertinggi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
