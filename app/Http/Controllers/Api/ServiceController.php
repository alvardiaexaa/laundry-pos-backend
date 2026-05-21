<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Layanan::all();

        return response()->json([
            'status' => 'success',
            'data' => $services
        ]);
    }

public function store(Request $request)
{
    if ($request->has('kategori')) {
        $request->merge([
            'kategori' => strtolower($request->kategori)
        ]);
    }

    $request->validate([
        'nama' => 'required|string|max:255',
        'harga' => 'required|integer|min:0',
        'satuan' => 'required|string|in:kg,item',
        'kategori' => 'required|string|in:normal,express' // Dijamin aman karena sudah di-lowercase
    ]);

    $service = Layanan::create([
        'nama' => $request->nama,
        'harga' => $request->harga,
        'satuan' => $request->satuan,
        'kategori' => $request->kategori
    ]);

    return response()->json([
        'status' => 'success',
        'data' => $service
    ], 201);
}
