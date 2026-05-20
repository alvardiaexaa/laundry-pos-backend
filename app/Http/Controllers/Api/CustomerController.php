<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Pelanggan::all();

        return response()->json([
            'status' => 'success',
            'data' => $customers
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_hp' => 'required|string|unique:pelanggans,nomor_hp',
            'alamat' => 'required|string'
        ]);

        $customer = Pelanggan::create([
            'nama' => $request->nama,
            'nomor_hp' => $request->nomor_hp,
            'alamat' => $request->alamat
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $customer
        ], 201);
    }
}
