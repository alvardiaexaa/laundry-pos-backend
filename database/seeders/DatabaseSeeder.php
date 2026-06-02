<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Omset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Cashier User
        $cashier1 = User::create([
            'name' => 'Siti Aminah',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $cashier2 = User::create([
            'name' => 'Budi Susanto',
            'email' => 'budi@example.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Seed Services (Layanan)
        // Normal Category
        $layananNormal1 = Layanan::create(['nama' => 'Cuci & Lipat', 'harga' => 10000, 'satuan' => 'kg', 'kategori' => 'normal']);
        $layananNormal2 = Layanan::create(['nama' => 'Setrika Saja', 'harga' => 8000, 'satuan' => 'kg', 'kategori' => 'normal']);
        $layananNormal3 = Layanan::create(['nama' => 'Cuci Kering Setrika', 'harga' => 12000, 'satuan' => 'kg', 'kategori' => 'normal']);
        $layananNormal4 = Layanan::create(['nama' => 'Pembersih Pakaian', 'harga' => 15000, 'satuan' => 'item', 'kategori' => 'normal']);
        Layanan::create(['nama' => 'Cuci Selimut', 'harga' => 20000, 'satuan' => 'item', 'kategori' => 'normal']);
        Layanan::create(['nama' => 'Cuci Gorden', 'harga' => 15000, 'satuan' => 'kg', 'kategori' => 'normal']);
        Layanan::create(['nama' => 'Cuci Seprai', 'harga' => 15000, 'satuan' => 'item', 'kategori' => 'normal']);
        Layanan::create(['nama' => 'Permak Pakaian', 'harga' => 15000, 'satuan' => 'item', 'kategori' => 'normal']);
        Layanan::create(['nama' => 'Cuci Bed Cover', 'harga' => 25000, 'satuan' => 'item', 'kategori' => 'normal']);
        Layanan::create(['nama' => 'Cuci Sepatu', 'harga' => 30000, 'satuan' => 'pasang', 'kategori' => 'normal']);

        // Express Category
        $layananExpress1 = Layanan::create(['nama' => 'Cuci & Lipat', 'harga' => 15000, 'satuan' => 'kg', 'kategori' => 'express']);
        $layananExpress2 = Layanan::create(['nama' => 'Setrika Saja', 'harga' => 12000, 'satuan' => 'kg', 'kategori' => 'express']);
        $layananExpress3 = Layanan::create(['nama' => 'Cuci Kering Setrika', 'harga' => 18000, 'satuan' => 'kg', 'kategori' => 'express']);
        $layananExpress4 = Layanan::create(['nama' => 'Pembersih Pakaian', 'harga' => 22000, 'satuan' => 'item', 'kategori' => 'express']);
        Layanan::create(['nama' => 'Cuci Selimut', 'harga' => 30000, 'satuan' => 'item', 'kategori' => 'express']);
        Layanan::create(['nama' => 'Cuci Gorden', 'harga' => 22000, 'satuan' => 'kg', 'kategori' => 'express']);
        Layanan::create(['nama' => 'Cuci Seprai', 'harga' => 22000, 'satuan' => 'item', 'kategori' => 'express']);
        Layanan::create(['nama' => 'Permak Pakaian', 'harga' => 25000, 'satuan' => 'item', 'kategori' => 'express']);
        Layanan::create(['nama' => 'Cuci Bed Cover', 'harga' => 37000, 'satuan' => 'item', 'kategori' => 'express']);
        Layanan::create(['nama' => 'Cuci Sepatu', 'harga' => 45000, 'satuan' => 'pasang', 'kategori' => 'express']);

        // 3. Seed Customers (Pelanggan)
        $customer1 = Pelanggan::create(['nama' => 'Amri Pratama', 'nomor_hp' => '08123456789', 'alamat' => 'Tenggilis Mejoyo']);
        $customer2 = Pelanggan::create(['nama' => 'Rina Saputri', 'nomor_hp' => '08129876543', 'alamat' => 'Rungkut Industri']);
        $customer3 = Pelanggan::create(['nama' => 'Fajar Nugroho', 'nomor_hp' => '08567891234', 'alamat' => 'Prapen Indah']);
        $customer4 = Pelanggan::create(['nama' => 'Dwi Lestari', 'nomor_hp' => '08991234567', 'alamat' => 'Jemursari']);

        // 4. Seed Transaction Records (Transaksi & DetailTransaksi)
        // Transaction 1: Amri Pratama (2 days ago)
        // Service: Cuci kering setrika (Normal) x 2 kg -> Subtotal: 24.000, Tax (11%): 2.640, Total: 26.640
        $date1 = Carbon::now()->subDays(2)->setTime(9, 15, 0);
        $tx1 = Transaksi::create([
            'pelanggan_id'      => $customer1->id,
            'invoice'           => '1001',
            'nama_pelanggan'    => $customer1->nama,
            'nomor_hp'          => $customer1->nomor_hp,
            'alamat'            => $customer1->alamat,
            'total_harga'       => 26640,
            'status_pembayaran'  => 'selesai',
            'catatan'           => 'Cuci bersih, jangan terlalu wangi',
            'metode_pembayaran' => 'cash',
            'kasir'             => 'Siti Aminah',
            'created_at'        => $date1,
            'updated_at'        => $date1
        ]);
        DetailTransaksi::create([
            'transaksi_id' => $tx1->id,
            'layanan_id'   => $layananNormal3->id,
            'jumlah'       => 2,
            'subtotal'     => 24000,
            'created_at'   => $date1,
            'updated_at'   => $date1
        ]);

        // Transaction 2: Rina Saputri (1 day ago)
        // Service: Cuci kering setrika (Normal) x 3 kg -> Subtotal: 36.000, Tax (11%): 3.960, Total: 39.960
        $date2 = Carbon::now()->subDays(1)->setTime(13, 45, 0);
        $tx2 = Transaksi::create([
            'pelanggan_id'      => $customer2->id,
            'invoice'           => '1002',
            'nama_pelanggan'    => $customer2->nama,
            'nomor_hp'          => $customer2->nomor_hp,
            'alamat'            => $customer2->alamat,
            'total_harga'       => 39960,
            'status_pembayaran'  => 'diambil',
            'catatan'           => 'Lipat rapi saja',
            'metode_pembayaran' => 'tf',
            'kasir'             => 'Budi Susanto',
            'created_at'        => $date2,
            'updated_at'        => $date2
        ]);
        DetailTransaksi::create([
            'transaksi_id' => $tx2->id,
            'layanan_id'   => $layananNormal3->id,
            'jumlah'       => 3,
            'subtotal'     => 36000,
            'created_at'   => $date2,
            'updated_at'   => $date2
        ]);

        // Transaction 3: Fajar Nugroho (4 hours ago)
        // Service: Pembersih Pakaian (Express) x 2 items -> Subtotal: 44.000, Tax (11%): 4.840, Total: 48.840
        $date3 = Carbon::now()->setTime(10, 30, 0);
        $tx3 = Transaksi::create([
            'pelanggan_id'      => $customer3->id,
            'invoice'           => '1003',
            'nama_pelanggan'    => $customer3->nama,
            'nomor_hp'          => $customer3->nomor_hp,
            'alamat'            => $customer3->alamat,
            'total_harga'       => 48840,
            'status_pembayaran'  => 'selesai',
            'catatan'           => 'Gantung jas warna hitam',
            'metode_pembayaran' => 'qris',
            'kasir'             => 'Siti Aminah',
            'created_at'        => $date3,
            'updated_at'        => $date3
        ]);
        DetailTransaksi::create([
            'transaksi_id' => $tx3->id,
            'layanan_id'   => $layananExpress4->id,
            'jumlah'       => 2,
            'subtotal'     => 44000,
            'created_at'   => $date3,
            'updated_at'   => $date3
        ]);

        // Transaction 4: Dwi Lestari (30 mins ago)
        // Service: Cuci & Lipat (Express) x 2 kg -> Subtotal: 30.000, Tax (11%): 3.300, Total: 33.300
        $date4 = Carbon::now()->setTime(15, 20, 0);
        $tx4 = Transaksi::create([
            'pelanggan_id'      => $customer4->id,
            'invoice'           => '1004',
            'nama_pelanggan'    => $customer4->nama,
            'nomor_hp'          => $customer4->nomor_hp,
            'alamat'            => $customer4->alamat,
            'total_harga'       => 33300,
            'status_pembayaran'  => 'proses',
            'catatan'           => 'Jangan dicampur dengan baju putih',
            'metode_pembayaran' => 'cash',
            'kasir'             => 'Budi Susanto',
            'created_at'        => $date4,
            'updated_at'        => $date4
        ]);
        DetailTransaksi::create([
            'transaksi_id' => $tx4->id,
            'layanan_id'   => $layananExpress1->id,
            'jumlah'       => 2,
            'subtotal'     => 30000,
            'created_at'   => $date4,
            'updated_at'   => $date4
        ]);

        // 5. Seed Omsets (aggregate calculations)
        Omset::create(['tanggal' => $date1->toDateString(), 'total_omset' => 26640]);
        Omset::create(['tanggal' => $date2->toDateString(), 'total_omset' => 39960]);
        
        $todayOmsetTotal = 0;
        if ($date3->isToday()) $todayOmsetTotal += 48840;
        if ($date4->isToday()) $todayOmsetTotal += 33300;
        
        if ($todayOmsetTotal > 0) {
            Omset::create(['tanggal' => Carbon::today()->toDateString(), 'total_omset' => $todayOmsetTotal]);
        }
    }
}
