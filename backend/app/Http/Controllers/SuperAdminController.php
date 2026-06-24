<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SuperAdminController extends Controller
{
    public function getAdmins()
    {
        $admins = User::whereIn('role', ['superadmin', 'admin'])->with('adminProfile')->get();
        return response()->json($admins);
    }

    public function createAdmin(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'level' => 'required|in:super_admin,admin',
            'kontak' => 'required|string|max:20'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['level'] == 'super_admin' ? 'superadmin' : 'admin'
        ]);

        $user->adminProfile()->create([
            'nama_admin' => $data['name'],
            'level' => $data['level'],
            'kontak' => $data['kontak']
        ]);

        // Log action
        \App\Models\AdminLog::create([
            'id_admin' => $request->user()->id,
            'action' => 'create_admin',
            'target_id' => $user->id,
            'target_type' => 'user',
            'description' => 'Membuat akun administrator baru: "' . $user->name . '" dengan level akses "' . ($data['level'] === 'super_admin' ? 'Super Admin' : 'Admin') . '"',
        ]);

        return response()->json(['message' => 'Admin created successfully', 'user' => $user->load('adminProfile')], 201);
    }

    public function deleteAdmin($id)
    {
        return response()->json([
            'message' => 'Akun administrator tidak dapat dihapus. Silakan gunakan fitur nonaktifkan akun.'
        ], 400);
    }
}
