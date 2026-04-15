<?php

namespace App\Http\Controllers;

use App\Models\ProdukInovasi;
use Illuminate\Http\Request;

class InisiatorController extends Controller
{
    public function getMyProducts(Request $request)
    {
        $user = $request->user();
        if (!$user->inisiatorProfile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        $products = ProdukInovasi::where('id_inisiator', $user->inisiatorProfile->id)->with(['opd', 'bentukInovasi', 'tahapanInovasi'])->get();
        return response()->json($products);
    }

    public function submitProduct(Request $request)
    {
        $user = $request->user();
        if (!$user->inisiatorProfile) {
            return response()->json(['message' => 'Inisiator profile required'], 403);
        }

        $data = $request->validate([
            'nama_inovasi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tahun_inovasi' => 'required|digits:4',
            'id_opd' => 'required|exists:opds,id',
            'id_bentuk' => 'required|exists:bentuk_inovasis,id',
            'id_tahapan' => 'required|exists:tahapan_inovasis,id',
            'is_digital' => 'required|boolean'
        ]);

        $data['id_inisiator'] = $user->inisiatorProfile->id;
        $data['status_kurasi'] = 'pending';

        $product = ProdukInovasi::create($data);

        return response()->json(['message' => 'Product submitted successfully', 'product' => $product], 201);
    }
}
