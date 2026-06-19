<?php

namespace App\Http\Controllers;

use App\Models\ProdukInovasi;
use App\Models\Masyarakat;
use App\Models\Pemerintah;
use App\Models\OPD;
use App\Models\BentukInovasi;
use App\Models\TahapanInovasi;
use App\Models\JenisInisiator;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
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
            'kecamatans' => Kecamatan::all(),
            'kelurahans' => Kelurahan::all(),

            // Ini sudah benar, pertahankan!
            'masyarakats' => Masyarakat::all(),
            'pemerintahs' => Pemerintah::all(),
            
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
            ->with([
                'mediaInovasi', 
                'opd', 
                'bentukInovasi', 
                'tahapanInovasi',
                'inisiatorProfile', // Tarik juga profilnya jika Vue butuh nama/jenis inisiator
                'adminProfile.user' // Tarik profil admin dan model user (email)
            ])
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
            'id_opd' => 'nullable|exists:opds,id_opd',
            'id_pemerintah' => 'nullable|exists:pemerintahs,id_pemerintah',
            'id_masyarakat' => 'nullable|exists:masyarakats,id_masyarakat', 
            'kontak' => 'required|string|max:255',
            'id_kecamatan' => 'required|exists:kecamatans,id',
            'id_kelurahan' => 'required|exists:kelurahans,id',
            'link_marketplace' => 'nullable|string|max:500',
            'is_digital' => 'required|boolean',
            'file_dokumentasi' => 'nullable|array|max:10', // Validasi untuk multiple files
            'file_dokumentasi.*' => 'file|max:10240' // Validasi untuk multiple files
        ]);

        // Update atau lengkapi profil inisiator yang sedang login
        $user->inisiatorProfile->update([
            'nama_inisiator' => $request->input('nama_inisiator'),
            'id_jenis_inisiator' => $request->input('id_jenis_inisiator'),
            'kontak' => $request->input('kontak'),
            'id_kelurahan' => $request->input('id_kelurahan'),
            'id_kecamatan' => $request->input('id_kecamatan')
        ]);

        // 🛠️ PERBAIKAN: Simpan data id_opd / id_masyarakat / id_pemerintah secara dinamis ke produk
        $product = ProdukInovasi::create([
            'id_inisiator' => $user->inisiatorProfile->id,
            'id_bentuk' => $request->input('id_bentuk'),
            'id_tahapan' => TahapanInovasi::first()?->id ?? 1,
            'nama_inovasi' => $request->input('nama_inovasi'),
            'deskripsi' => $request->input('deskripsi'),
            'tahun_inovasi' => $request->input('tahun_inovasi'),
            'status_kurasi' => 'pending',
            'is_digital' => $request->input('is_digital'),
            
            // Mengambil input kondisional dari Form Vue
            'id_opd' => $request->input('id_opd'),
            'id_masyarakat' => $request->input('id_masyarakat'),
            'id_pemerintah' => $request->input('id_pemerintah'),
            'id_kecamatan' => $request->input('id_kecamatan'),
            'id_kelurahan' => $request->input('id_kelurahan'),
            'kontak' => $request->input('kontak'),
        ]);

        // Save file dokumentasi
        if ($request->hasFile('file_dokumentasi')) {
        foreach ($request->file('file_dokumentasi') as $index => $file) {
        $fileName = time() . '_' . $index . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads'), $fileName);
        
        $product->mediaInovasi()->create([
            'jenis_media' => 'file',
            'isi_konten' => '/uploads/' . $fileName,
            'is_primary' => $index === 0, // File pertama jadi primary image/cover
            'urutan' => $index
        ]);
        }
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
            'id_opd' => 'nullable|exists:opds,id_opd',
            'id_pemerintah' => 'nullable|exists:pemerintahs,id_pemerintah',
            'id_masyarakat' => 'nullable|exists:masyarakats,id_masyarakat', 
            'kontak' => 'required|string|max:255',
            'id_kecamatan' => 'required|exists:kecamatans,id',
            'id_kelurahan' => 'required|exists:kelurahans,id',
            'link_marketplace' => 'nullable|string|max:500',
            'is_digital' => 'required|boolean',
            'file_dokumentasi' => 'nullable|array|max:10',
            'file_dokumentasi.*' => 'file|max:10240'
        ]);

        // Update inisiator profile
        $user->inisiatorProfile->update([
            'nama_inisiator' => $request->input('nama_inisiator'),
            'id_jenis_inisiator' => $request->input('id_jenis_inisiator'),
            'kontak' => $request->input('kontak'),
            'id_kecamatan' => $request->input('id_kecamatan'),
            'id_kelurahan' => $request->input('id_kelurahan')
        ]);

        // 🛠️ PERBAIKAN: Update juga kolom kondisionalnya
        $updateData = [
            'nama_inovasi' => $request->input('nama_inovasi'),
            'deskripsi' => $request->input('deskripsi'),
            'tahun_inovasi' => $request->input('tahun_inovasi'),
            'id_bentuk' => $request->input('id_bentuk'),
            'is_digital' => filter_var($request->input('is_digital', $product->is_digital), FILTER_VALIDATE_BOOLEAN),
            
            'id_tahapan' => $request->input('id_tahapan'),
            'id_opd' => $request->input('id_opd'),
            'id_masyarakat' => $request->input('id_masyarakat'),
            'id_pemerintah' => $request->input('id_pemerintah'),
            'id_kecamatan' => $request->input('id_kecamatan'),
            'id_kelurahan' => $request->input('id_kelurahan'),
            'kontak' => $request->input('kontak'),
        ];

        if ($product->status_kurasi === 'rejected') {
            $updateData['status_kurasi'] = 'pending';
            $updateData['alasan_penolakan'] = null;
            $updateData['tanggal_review'] = null;
            $updateData['id_admin'] = null;
        }

        $product->update($updateData);

        // Hapus file lama yang sudah tidak ada di list existing_files Vue
        $existingInVue = json_decode($request->input('existing_files', '[]'), true);
        $oldMedias = $product->mediaInovasi()->where('jenis_media', 'file')->get();

        foreach ($oldMedias as $oldMedia) {
            if (!in_array($oldMedia->isi_konten, $existingInVue)) {
                $oldPath = public_path(ltrim($oldMedia->isi_konten, '/'));
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
                $oldMedia->delete();
            }
        }

        // Simpan file baru tambahan (jika ada)
        if ($request->hasFile('file_dokumentasi')) {
            // Ambil nomor urutan terakhir yang tersisa agar tidak bentrok
            $lastUrutan = $product->mediaInovasi()->where('jenis_media', 'file')->max('urutan') ?? -1;
            
            foreach ($request->file('file_dokumentasi') as $index => $file) {
                $fileName = time() . '_' . $index . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $fileName);
                
                $product->mediaInovasi()->create([
                    'jenis_media' => 'file',
                    'isi_konten' => '/uploads/' . $fileName,
                    'is_primary' => ($lastUrutan === -1 && $index === 0), 
                    'urutan' => $lastUrutan + 1 + $index
                ]);
            }
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

    /**
     * Fitur 2: Ajukan ulang produk yang ditolak — reset status ke pending
     */
    public function resubmitProduct(Request $request, $id)
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

        if ($product->status_kurasi !== 'rejected') {
            return response()->json(['message' => 'Hanya produk yang ditolak yang bisa diajukan ulang.'], 400);
        }

        $product->update([
            'status_kurasi' => 'pending',
            'alasan_penolakan' => null,
            'tanggal_review' => null,
            'id_admin' => null,
        ]);

        return response()->json(['message' => 'Produk berhasil diajukan ulang.', 'product' => $product]);
    }
}