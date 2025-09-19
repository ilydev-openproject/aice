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
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->string('nama_toko');
            $table->string('kode_toko')->nullable()->unique(); // opsional, tapi unik jika diisi
            $table->time('jam_buka')->nullable();
            $table->time('jam_tutup')->nullable();
            $table->string('nomor_wa')->nullable(); // format: 628123456789
            $table->string('link')->nullable(); // format: 628123456789
            $table->text('alamat')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlets');
    }
};
