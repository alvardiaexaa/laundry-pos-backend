<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->nullable()->constrained('pelanggans')->onDelete('set null');
            $table->string('invoice')->unique();
            $table->string('nama_pelanggan');
            $table->string('nomor_hp');
            $table->text('alamat');
            $table->integer('total_harga');
            $table->string('status_pembayaran')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
