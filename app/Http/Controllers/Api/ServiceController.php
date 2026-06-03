<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Layanan::orderBy('urutan', 'asc')->get();

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

    public function reorder(Request $request)
    {
        $request->validate([
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'integer|exists:layanans,id'
        ]);

        $ids = $request->ordered_ids;
        foreach ($ids as $index => $id) {
            Layanan::where('id', $id)->update(['urutan' => $index]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Urutan berhasil diperbarui'
        ]);
    }

    public function update(Request $request, $id)
    {
        $service = Layanan::find($id);
        if (!$service) {
            return response()->json(['status' => 'error', 'message' => 'Layanan tidak ditemukan'], 404);
        }

        if ($request->has('kategori')) {
            $request->merge([
                'kategori' => strtolower($request->kategori)
            ]);
        }

        $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'harga' => 'sometimes|required|integer|min:0',
            'satuan' => 'sometimes|required|string|in:kg,item,pasang',
            'kategori' => 'sometimes|required|string|in:normal,express'
        ]);

        $service->update($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $service
        ]);
    }

    public function destroy($id)
    {
        $service = Layanan::find($id);
        if (!$service) {
            return response()->json(['status' => 'error', 'message' => 'Layanan tidak ditemukan'], 404);
        }

        $service->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Layanan berhasil dihapus'
        ]);
    }
}
