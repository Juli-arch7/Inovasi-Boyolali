<?php

namespace App\Http\Controllers;

use App\Models\ProdukInovasi;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getSubmissions()
    {
        $products = ProdukInovasi::with(['inisiatorProfile', 'opd', 'bentukInovasi', 'tahapanInovasi'])->get();
        return response()->json($products);
    }

    public function verifyProduct(Request $request, $id)
    {
        $request->validate([
            'status_kurasi' => 'required|in:approved,rejected,pending'
        ]);

        $product = ProdukInovasi::findOrFail($id);
        $product->update([
            'status_kurasi' => $request->status_kurasi,
            'id_admin' => $request->user()->adminProfile?->id
        ]);

        return response()->json(['message' => 'Product status updated', 'product' => $product]);
    }

    public function getProductDetail($id)
    {
        $product = ProdukInovasi::with(['inisiatorProfile', 'opd', 'bentukInovasi', 'tahapanInovasi'])->findOrFail($id);
        return response()->json($product);
    }

    public function getUsers()
    {
        $users = \App\Models\User::with(['inisiatorProfile', 'adminProfile'])->get();
        return response()->json($users);
    }

    public function deleteUser($id)
    {
        $user = \App\Models\User::findOrFail($id);
        if ($user->role === 'superadmin' && \App\Models\User::where('role', 'superadmin')->count() <= 1) {
            return response()->json(['message' => 'Cannot delete the last superadmin'], 400);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
