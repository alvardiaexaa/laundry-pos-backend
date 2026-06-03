<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Pelanggan;
use App\Models\DetailTransaksi;
use App\Models\Omset;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nomor' => 'required|string|max:20',
            'alamat' => 'required|string',
            'total' => 'required|integer|min:0',
            'cart' => 'nullable|array',
            'catatan' => 'nullable|string',
            'metode_pembayaran' => 'nullable|string',
            'kasir' => 'nullable|string'
        ]);

        $pelanggan = Pelanggan::firstOrCreate(
            ['nomor_hp' => $request->nomor],
            ['nama' => $request->nama, 'alamat' => $request->alamat]
        );

        $lastTransaksi = Transaksi::orderBy('id', 'desc')->first();
        $nextInvoice = ($lastTransaksi && is_numeric($lastTransaksi->invoice)) 
            ? intval($lastTransaksi->invoice) + 1 
            : 1001;
        $invoice = (string)$nextInvoice;

        $order = Transaksi::create([
            'pelanggan_id'     => $pelanggan->id,
            'invoice'          => $invoice,
            'nama_pelanggan'   => $request->nama,
            'nomor_hp'         => $request->nomor,
            'alamat'           => $request->alamat,
            'total_harga'      => $request->total,
            'status_pembayaran' => 'antri',
            'catatan'          => $request->catatan,
            'metode_pembayaran'=> $request->metode_pembayaran ?? 'cash',
            'kasir'            => $request->kasir ?? 'Siti Aminah'
        ]);

        if ($request->has('cart') && is_array($request->cart)) {
            foreach ($request->cart as $item) {
                DetailTransaksi::create([
                    'transaksi_id' => $order->id,
                    'layanan_id'   => $item['id'],
                    'jumlah'       => $item['quantity'],
                    'subtotal'     => $item['price'] * $item['quantity']
                ]);
            }
        }

        $todayDate = Carbon::today()->toDateString();
        $omset = Omset::firstOrCreate(['tanggal' => $todayDate]);
        $omset->increment('total_omset', $request->total);

        // Menggunakan optional load untuk mencegah error jika relasi belum dibuat di model
        $relations = [];
        if (method_exists($order, 'details')) $relations[] = 'details.layanan';
        if (method_exists($order, 'pelanggan')) $relations[] = 'pelanggan';

        if (!empty($relations)) {
            $order->load($relations);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $order
        ]);
    }

    public function index()
    {
        $query = Transaksi::query();

        // Cek keamanan relasi sebelum dipanggil
        if (method_exists(Transaksi::class, 'details') && method_exists(Transaksi::class, 'pelanggan')) {
            $query->with(['details.layanan', 'pelanggan']);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data'   => $orders
        ]);
    }

    public function dashboardStats()
    {
        $today = Carbon::today();

        $orderHariIni = Transaksi::whereDate('created_at', $today)->count();
        $pelangganAktif = Transaksi::distinct('pelanggan_id')->count('pelanggan_id');

        $pendingCount = Transaksi::whereIn('status_pembayaran', ['antri', 'proses', 'pending'])->count();
        $totalCount = Transaksi::count();
        $prosesString = $pendingCount . '/' . $totalCount;

        $pemasukanHariIni = Transaksi::whereDate('created_at', $today)->sum('total_harga');

        $queryLatest = Transaksi::query();
        if (method_exists(Transaksi::class, 'details') && method_exists(Transaksi::class, 'pelanggan')) {
            $queryLatest->with(['details.layanan', 'pelanggan']);
        }

        $latestTransactions = $queryLatest->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'order_hari_ini' => (int)$orderHariIni,
                'pelanggan_aktif' => (int)$pelangganAktif,
                'proses' => $prosesString,
                'pemasukan_hari_ini' => (int)$pemasukanHariIni,
                'latest_transactions' => $latestTransactions
            ]
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:antri,proses,selesai,diambil,batal'
        ]);

        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        $transaksi->update([
            'status_pembayaran' => $request->status
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status transaksi berhasil diperbarui',
            'data' => $transaksi
        ]);
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        // Hapus detail transaksi terlebih dahulu untuk menjaga relasi
        $transaksi->details()->delete();
        $transaksi->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi berhasil dihapus'
        ]);
    }

    public function updateCustomerInfo(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nomor' => 'required|string|max:20',
            'alamat' => 'required|string'
        ]);

        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        $transaksi->update([
            'nama_pelanggan' => $request->nama,
            'nomor_hp' => $request->nomor,
            'alamat' => $request->alamat
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Informasi pelanggan berhasil diperbarui',
            'data' => $transaksi
        ]);
    }
}
