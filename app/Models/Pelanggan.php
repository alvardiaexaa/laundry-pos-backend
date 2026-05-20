<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $fillable = ['nama', 'nomor_hp', 'alamat'];

    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }
}
