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
            'level' => 'required|in:super_admin,admin'
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
            'level' => $data['level']
        ]);

        return response()->json(['message' => 'Admin created successfully', 'user' => $user->load('adminProfile')], 201);
    }

    public function deleteAdmin($id)
    {
        $user = User::findOrFail($id);
        if ($user->role === 'superadmin' && User::where('role', 'superadmin')->count() <= 1) {
            return response()->json(['message' => 'Cannot delete the last superadmin'], 400);
        }
        $user->delete();
        return response()->json(['message' => 'Admin deleted successfully']);
    }
}
