<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $order = Transaksi::create([
            'invoice'          => 'INV-' . time(),
            'nama_pelanggan'   => $request->nama,
            'nomor_hp'         => $request->nomor,
            'alamat'           => $request->alamat,
            'total_harga'      => $request->total,
            'status_pembayaran' => 'success'
        ]);

        return response()->json([
            'status' => 'success',
            'data'   => $order
        ]);
    }
}
