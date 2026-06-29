<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'nullable|string'
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        // Depending on role, create profiles
        if ($user->role === 'masyarakat') {
            $user->masyarakatProfile()->create(['nama_lengkap' => $user->name ?? $user->username]);
        } elseif (!$user->role || $user->role === 'inisiator') {
            $user->role = 'inisiator';
            $user->save();
            $jenis = \App\Models\JenisInisiator::where('nama_jenis_inisiator', 'Masyarakat')->first();
            $user->inisiatorProfile()->create([
                'nama_inisiator' => $user->name ?? $user->username,
                'id_jenis_inisiator' => $jenis ? $jenis->id : 5,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['message' => 'User created successfully', 'user' => $user, 'token' => $token], 201);
    }

    public function login(Request $request) {
        $data = $request->validate([
            'email' => 'required|string', // This can be email or username
            'password' => 'required|string'
        ]);

        $user = User::where('email', $data['email'])
                    ->orWhere('username', $data['email'])
                    ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Fitur 5: Cek apakah akun dinonaktifkan
        if (!$user->is_active) {
            return response()->json(['message' => 'Akun Anda telah dinonaktifkan. Hubungi admin untuk informasi lebih lanjut.'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->load(['adminProfile', 'inisiatorProfile']);
        return response()->json(['message' => 'Login successful', 'user' => $user, 'token' => $token], 200);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request) {
        $user = $request->user();
        $user->load(['adminProfile', 'inisiatorProfile']);
        return response()->json($user);
    }
}
