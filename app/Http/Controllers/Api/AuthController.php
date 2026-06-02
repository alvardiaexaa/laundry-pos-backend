<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi Input Frontend
        $request->validate([
            'username' => 'required', // Bisa berisi email / nama
            'password' => 'required',
        ]);

        $username = $request->username;
        $password = $request->password;

        // 2. Cek Akun HARDCODED ADMIN (admin@example.com)
        if ($username === 'admin@example.com' && $password === 'admin123') {
            return response()->json([
                'status' => 'success',
                'role' => 'admin',
                'redirect_to' => '/dashboard/AdminDashboard',
                'data' => [
                    'user' => [
                        'id' => 0,
                        'name' => 'Admin',
                        'email' => 'admin@example.com',
                        'outlet' => 'Pusat'
                    ]
                ]
            ]);
        }

        // 3. Fitur Auto-Create User Test (Jika database masih kosong)
        if (User::count() === 0) {
            User::create([
                'name' => 'Siti Aminah',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
            ]);
            User::create([
                'name' => 'Budi Susanto',
                'email' => 'budi@example.com',
                'password' => Hash::make('password'),
            ]);
        }

        // 4. Cari User Biasa di Database (Mendukung test@example.com)
        $user = User::where('email', $username)
                    ->orWhere('name', $username)
                    ->first();

        // 5. Validasi jika user tidak ditemukan atau password salah
        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Username atau password salah'
            ], 401);
        }

        // 6. Respon Sukses untuk User Biasa / Kasir
        return response()->json([
            'status' => 'success',
            'role' => 'user',
            'redirect_to' => '/dashboard',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'outlet' => 'Tenggilis Mejoyo'
                ]
            ]
        ]);
    }

    public function logout(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }
}
