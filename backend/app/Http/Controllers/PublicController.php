<?php

namespace App\Http\Controllers;

use App\Models\ProdukInovasi;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\BentukInovasi;
use App\Models\OPD;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function getCuratedProducts()
    {
        $products = ProdukInovasi::where('status_kurasi', 'approved')
            ->with(['inisiatorProfile.kelurahan.kecamatan', 'opd', 'bentukInovasi', 'tahapanInovasi', 'mediaInovasi'])
            ->get();
        return response()->json($products);
    }

    public function getProductDetail($id)
    {
        $product = ProdukInovasi::where('status_kurasi', 'approved')
            ->with(['inisiatorProfile.kelurahan.kecamatan', 'opd', 'bentukInovasi', 'tahapanInovasi', 'mediaInovasi', 'adminProfile'])
            ->findOrFail($id);
        return response()->json($product);
    }

    public function getMetadata()
    {
        return response()->json([
            'kecamatans' => Kecamatan::all(),
            'kelurahans' => Kelurahan::all(),
            'bentuk_inovasis' => BentukInovasi::all(),
            'opds' => OPD::all(),
        ]);
    }
}
