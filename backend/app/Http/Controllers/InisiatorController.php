<?php

namespace App\Http\Controllers;

use App\Models\ProdukInovasi;
use App\Models\OPD;
use App\Models\BentukInovasi;
use App\Models\TahapanInovasi;
use App\Models\JenisInisiator;
use Illuminate\Http\Request;

class InisiatorController extends Controller
{
    public function getMetadata(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'opds' => OPD::all(),
            'bentuk_inovasis' => BentukInovasi::all(),
            'tahapan_inovasis' => TahapanInovasi::all(),
            'jenis_inisiators' => JenisInisiator::all(),
            'kecamatans' => \App\Models\Kecamatan::all(),
            'kelurahans' => \App\Models\Kelurahan::all(),
            'inisiator_profile' => $user->inisiatorProfile ? $user->inisiatorProfile->load('kelurahan') : null
        ]);
    }

    public function getMyProducts(Request $request)
    {
        $user = $request->user();
        if (!$user->inisiatorProfile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        $products = ProdukInovasi::where('id_inisiator', $user->inisiatorProfile->id)->with(['opd', 'bentukInovasi', 'tahapanInovasi'])->get();
        return response()->json($products);
    }

    public function getProductDetail(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->inisiatorProfile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        $product = ProdukInovasi::where('id', $id)
            ->where('id_inisiator', $user->inisiatorProfile->id)
            ->with(['mediaInovasi'])
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found or access denied'], 404);
        }

        return response()->json($product);
    }

    public function submitProduct(Request $request)
    {
        $user = $request->user();
        if (!$user->inisiatorProfile) {
            return response()->json(['message' => 'Inisiator profile required'], 403);
        }

        $request->validate([
            'nama_inovasi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tahun_inovasi' => 'required|digits:4',
            'id_bentuk' => 'required|exists:bentuk_inovasis,id',
            'nama_inisiator' => 'required|string|max:255',
            'id_jenis_inisiator' => 'required|exists:jenis_inisiators,id',
            'kontak' => 'required|string|max:255',
            'id_kelurahan' => 'required|exists:kelurahans,id',
            'link_marketplace' => 'nullable|string|max:500',
            'file_dokumentasi' => 'nullable|file|max:10240'
        ]);

        // Update inisiator profile
        $user->inisiatorProfile->update([
            'nama_inisiator' => $request->input('nama_inisiator'),
            'id_jenis_inisiator' => $request->input('id_jenis_inisiator'),
            'kontak' => $request->input('kontak'),
            'id_kelurahan' => $request->input('id_kelurahan')
        ]);

        // Create product
        $product = ProdukInovasi::create([
            'id_inisiator' => $user->inisiatorProfile->id,
            'id_opd' => OPD::first()?->id ?? 1,
            'id_bentuk' => $request->input('id_bentuk'),
            'id_tahapan' => TahapanInovasi::first()?->id ?? 1,
            'nama_inovasi' => $request->input('nama_inovasi'),
            'deskripsi' => $request->input('deskripsi'),
            'tahun_inovasi' => $request->input('tahun_inovasi'),
            'status_kurasi' => 'pending',
            'is_digital' => false
        ]);

        // Save file dokumentasi
        if ($request->hasFile('file_dokumentasi')) {
            $file = $request->file('file_dokumentasi');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $fileName);
            $product->mediaInovasi()->create([
                'jenis_media' => 'file',
                'isi_konten' => '/uploads/' . $fileName,
                'is_primary' => true,
                'urutan' => 0
            ]);
        }

        // Save marketplace link
        if ($request->filled('link_marketplace')) {
            $product->mediaInovasi()->create([
                'jenis_media' => 'link',
                'isi_konten' => $request->input('link_marketplace'),
                'is_primary' => false,
                'urutan' => 1
            ]);
        }

        return response()->json(['message' => 'Product submitted successfully', 'product' => $product], 201);
    }

    public function updateProduct(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->inisiatorProfile) {
            return response()->json(['message' => 'Inisiator profile required'], 403);
        }

        $product = ProdukInovasi::where('id', $id)
            ->where('id_inisiator', $user->inisiatorProfile->id)
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found or access denied'], 404);
        }

        $request->validate([
            'nama_inovasi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tahun_inovasi' => 'required|digits:4',
            'id_bentuk' => 'required|exists:bentuk_inovasis,id',
            'nama_inisiator' => 'required|string|max:255',
            'id_jenis_inisiator' => 'required|exists:jenis_inisiators,id',
            'kontak' => 'required|string|max:255',
            'id_kelurahan' => 'required|exists:kelurahans,id',
            'link_marketplace' => 'nullable|string|max:500',
            'file_dokumentasi' => 'nullable|file|max:10240'
        ]);

        // Update inisiator profile
        $user->inisiatorProfile->update([
            'nama_inisiator' => $request->input('nama_inisiator'),
            'id_jenis_inisiator' => $request->input('id_jenis_inisiator'),
            'kontak' => $request->input('kontak'),
            'id_kelurahan' => $request->input('id_kelurahan')
        ]);

        // Update product
        $product->update([
            'nama_inovasi' => $request->input('nama_inovasi'),
            'deskripsi' => $request->input('deskripsi'),
            'tahun_inovasi' => $request->input('tahun_inovasi'),
            'id_bentuk' => $request->input('id_bentuk'),
        ]);

        // Save file dokumentasi
        if ($request->hasFile('file_dokumentasi')) {
            $oldMedia = $product->mediaInovasi()->where('jenis_media', 'file')->first();
            if ($oldMedia) {
                $oldPath = public_path(ltrim($oldMedia->isi_konten, '/'));
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
                $oldMedia->delete();
            }

            $file = $request->file('file_dokumentasi');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $fileName);
            $product->mediaInovasi()->create([
                'jenis_media' => 'file',
                'isi_konten' => '/uploads/' . $fileName,
                'is_primary' => true,
                'urutan' => 0
            ]);
        }

        // Save marketplace link
        if ($request->filled('link_marketplace')) {
            $product->mediaInovasi()->updateOrCreate(
                ['jenis_media' => 'link'],
                ['isi_konten' => $request->input('link_marketplace'), 'is_primary' => false, 'urutan' => 1]
            );
        } else {
            $product->mediaInovasi()->where('jenis_media', 'link')->delete();
        }

        return response()->json(['message' => 'Product updated successfully', 'product' => $product], 200);
    }
}
