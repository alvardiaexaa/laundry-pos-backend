<?php
public function up() {
    Schema::create('transaksis', function (Blueprint $table) {
        $table->id();
        $table->string('invoice')->unique();
        $table->string('nama_pelanggan');
        $table->string('nomor_hp');
        $table->text('alamat');
        $table->integer('total_harga');
        $table->string('status_pembayaran')->default('pending');
        $table->timestamps();
    });
}
