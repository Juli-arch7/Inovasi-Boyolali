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
}
