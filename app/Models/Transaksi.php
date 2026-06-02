<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    protected $fillable = [
        'pelanggan_id',
        'invoice',
        'nama_pelanggan',
        'nomor_hp',
        'alamat',
        'total_harga',
        'status_pembayaran',
        'catatan',
        'metode_pembayaran',
        'kasir'
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}
