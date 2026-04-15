<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'username' => 'required|string|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'nullable|string'
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        // Depending on role, create profiles (we can simplify this for now or just wait for specific profile creation)
        if ($user->role === 'masyarakat') {
            $user->masyarakatProfile()->create(['nama_lengkap' => $user->name ?? $user->username]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['message' => 'User created successfully', 'user' => $user, 'token' => $token], 201);
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['message' => 'Login successful', 'user' => $user, 'token' => $token], 200);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request) {
        return response()->json($request->user());
    }
}
