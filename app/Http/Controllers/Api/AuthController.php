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

        // Set user to online
        $user->is_online = true;
        $user->save();

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
        $name = $request->input('name');
        if ($name) {
            $user = User::where('name', $name)->first();
            if ($user) {
                $user->is_online = false;
                $user->save();
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    public function cashiers()
    {
        $users = User::all()->map(function($user) {
            return [
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'KASIR',
                'status' => $user->is_online ? 'Online' : 'Offline',
                'initials' => strtoupper(substr($user->name, 0, 2)) // simple fallback
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    public function registerCashier(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_online' => false
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cashier created successfully',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'KASIR',
                'status' => 'Offline',
                'initials' => strtoupper(substr($user->name, 0, 2))
            ]
        ]);
    }

    public function updateCashier(Request $request, $email)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Karyawan tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:6',
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Cashier updated successfully',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'KASIR',
                'status' => $user->is_online ? 'Online' : 'Offline',
                'initials' => strtoupper(substr($user->name, 0, 2))
            ]
        ]);
    }

    public function deleteCashier($email)
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Cashier deleted successfully'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Cashier not found'
        ], 404);
    }
}
