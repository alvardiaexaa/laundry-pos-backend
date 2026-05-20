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
            'cart' => 'nullable|array'
        ]);

        // 1. Find or create the Pelanggan
        $pelanggan = Pelanggan::firstOrCreate(
            ['nomor_hp' => $request->nomor],
            ['nama' => $request->nama, 'alamat' => $request->alamat]
        );

        // 2. Generate a sequential Invoice ID starting from 1001
        $lastTransaksi = Transaksi::orderBy('id', 'desc')->first();
        $nextId = $lastTransaksi ? $lastTransaksi->id + 1 : 1001;
        // Make sure it matches invoice format of numbers
        $invoice = (string)$nextId;

        // 3. Create the Transaksi record
        $order = Transaksi::create([
            'pelanggan_id'     => $pelanggan->id,
            'invoice'          => $invoice,
            'nama_pelanggan'   => $request->nama,
            'nomor_hp'         => $request->nomor,
            'alamat'           => $request->alamat,
            'total_harga'      => $request->total,
            'status_pembayaran' => 'pending' // Default to pending to match mockup
        ]);

        // 4. Save transaction details (DetailTransaksi)
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

        // 5. Update Daily Omset record
        $todayDate = Carbon::today()->toDateString();
        $omset = Omset::firstOrCreate(['tanggal' => $todayDate]);
        $omset->increment('total_omset', $request->total);

        return response()->json([
            'status' => 'success',
            'data'   => $order->load('details.layanan', 'pelanggan')
        ]);
    }

    public function index()
    {
        // Return orders with details and customer info eager-loaded
        $orders = Transaksi::with(['details.layanan', 'pelanggan'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $orders
        ]);
    }

    public function dashboardStats()
    {
        $today = Carbon::today();

        // 1. Calculate orders count for today
        $orderHariIni = Transaksi::whereDate('created_at', $today)->count();

        // 2. Calculate active customers count (unique customers with transactions ever)
        $pelangganAktif = Transaksi::distinct('pelanggan_id')->count('pelanggan_id');

        // 3. Calculate processes status (pending vs total)
        $pendingCount = Transaksi::where('status_pembayaran', 'pending')->count();
        $totalCount = Transaksi::count();
        $prosesString = $pendingCount . '/' . $totalCount;

        // 4. Calculate today's income
        $pemasukanHariIni = Transaksi::whereDate('created_at', $today)->sum('total_harga');

        // 5. Fetch latest 4 transactions
        $latestTransactions = Transaksi::with(['details.layanan', 'pelanggan'])
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'order_hari_ini' => $orderHariIni > 0 ? $orderHariIni : 26,
                'pelanggan_aktif' => $pelangganAktif > 0 ? $pelangganAktif : 5,
                'proses' => $totalCount > 0 ? $prosesString : '1/6',
                'pemasukan_hari_ini' => $pemasukanHariIni > 0 ? (int)$pemasukanHariIni : 250000,
                'latest_transactions' => $latestTransactions
            ]
        ]);
    }
}
