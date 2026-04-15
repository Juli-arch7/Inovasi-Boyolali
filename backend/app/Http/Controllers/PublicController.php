<?php

namespace App\Http\Controllers;

use App\Models\ProdukInovasi;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function getCuratedProducts()
    {
        $products = ProdukInovasi::where('status_kurasi', 'approved')
            ->with(['inisiatorProfile', 'opd', 'bentukInovasi', 'tahapanInovasi', 'mediaInovasi'])
            ->get();
        return response()->json($products);
    }

    public function getProductDetail($id)
    {
        $product = ProdukInovasi::where('status_kurasi', 'approved')
            ->with(['inisiatorProfile', 'opd', 'bentukInovasi', 'tahapanInovasi', 'mediaInovasi', 'adminProfile'])
            ->findOrFail($id);
        return response()->json($product);
    }
}
