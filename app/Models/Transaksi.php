<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model {
    protected $fillable = ['invoice', 'nama_pelanggan', 'nomor_hp', 'alamat', 'total_harga', 'status_pembayaran'];
}
