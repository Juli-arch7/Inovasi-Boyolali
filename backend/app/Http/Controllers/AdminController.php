<?php

namespace App\Http\Controllers;

use App\Models\ProdukInovasi;
use App\Models\OPD;
use App\Models\User;
use App\Models\TahapanInovasi;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getStats()
    {
        $total = ProdukInovasi::count();
        $approved = ProdukInovasi::where('status_kurasi', 'approved')->count();
        $pending = ProdukInovasi::where('status_kurasi', 'pending')->count();
        $rejected = ProdukInovasi::where('status_kurasi', 'rejected')->count();
        $nonaktif = ProdukInovasi::where('is_active', false)->count();
        $totalOpd = OPD::count();
        $totalInisiator = User::where('role', 'inisiator')->count();
        $totalUsers = User::count();

        // Sum metrics
        $totalViews = ProdukInovasi::sum('views_count');
        $totalLikes = ProdukInovasi::sum('likes_count');
        $totalDownloads = ProdukInovasi::sum('downloads_count');

        // Inovasi per tahun (5 tahun terakhir)
        $currentYear = now()->year;
        $perTahun = [];
        for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
            $perTahun[] = [
                'tahun' => $y,
                'total' => ProdukInovasi::whereYear('created_at', $y)->count(),
                'approved' => ProdukInovasi::whereYear('created_at', $y)->where('status_kurasi', 'approved')->count(),
            ];
        }

        // Inovasi per bulan (12 bulan terakhir)
        $perBulan = [];
        for ($m = 11; $m >= 0; $m--) {
            $date = now()->subMonths($m);
            $perBulan[] = [
                'label' => $date->format('M Y'),
                'total' => ProdukInovasi::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
            ];
        }

        // Inovasi per bentuk (kategori)
        $perBentuk = \App\Models\BentukInovasi::all()->map(fn($b) => [
            'kategori' => $b->nama_bentuk,
            'total' => ProdukInovasi::where('id_bentuk', $b->id)->count(),
        ]);

        // Aktivitas user (Area Chart - last 7 days simulation)
        $aktivitasUser = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $aktivitasUser[] = [
                'tanggal' => $date->format('d M'),
                'aktif' => rand(150, 450),
                'kunjungan' => rand(500, 1500),
            ];
        }

        // Top 5 OPD by inovasi
        $topOpd = ProdukInovasi::selectRaw('id_opd, count(*) as total')
            ->groupBy('id_opd')
            ->orderByDesc('total')
            ->limit(5)
            ->with('opd')
            ->get()
            ->map(fn($p) => ['nama' => $p->opd?->nama_opd ?? 'Unknown', 'total' => $p->total]);

        // Produk terbaru (5)
        $terbaru = ProdukInovasi::with(['inisiatorProfile', 'opd'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'nama_inovasi' => $p->nama_inovasi,
                'status_kurasi' => $p->status_kurasi,
                'inisiator' => $p->inisiatorProfile?->nama_inisiator ?? '-',
                'opd' => $p->opd?->nama_opd ?? '-',
                'created_at' => $p->created_at?->format('d M Y'),
            ]);

        return response()->json([
            'total' => $total,
            'approved' => $approved,
            'pending' => $pending,
            'rejected' => $rejected,
            'nonaktif' => $nonaktif,
            'total_opd' => $totalOpd,
            'total_inisiator' => $totalInisiator,
            'total_users' => $totalUsers,
            'total_views' => $totalViews,
            'total_likes' => $totalLikes,
            'total_downloads' => $totalDownloads,
            'per_tahun' => $perTahun,
            'per_bulan' => $perBulan,
            'per_bentuk' => $perBentuk,
            'aktivitas_user' => $aktivitasUser,
            'top_opd' => $topOpd,
            'terbaru' => $terbaru,
        ]);
    }

    public function getSubmissions()
    {
        $products = ProdukInovasi::with(['inisiatorProfile', 'opd', 'bentukInovasi', 'tahapanInovasi'])->get();
        return response()->json($products);
    }

    public function verifyProduct(Request $request, $id)
    {
        $request->validate([
            'status_kurasi' => 'required|in:approved,rejected,pending',
            'alasan_penolakan' => 'required_if:status_kurasi,rejected|nullable|string'
        ]);

        $product = ProdukInovasi::findOrFail($id);
        $updateData = [
            'status_kurasi' => $request->status_kurasi,
            'id_admin' => $request->user()->adminProfile?->id,
            'tanggal_review' => now(),
        ];

        if ($request->status_kurasi === 'rejected') {
            $updateData['alasan_penolakan'] = $request->alasan_penolakan;
        } else {
            $updateData['alasan_penolakan'] = null;
        }

        $product->update($updateData);

        // Log action
        \App\Models\AdminLog::create([
            'id_admin' => $request->user()->id,
            'action' => 'verify_product',
            'target_id' => $product->id,
            'target_type' => 'product',
            'description' => ($request->status_kurasi === 'approved' ? 'Menyetujui' : 'Menolak') . ' produk inovasi "' . $product->nama_inovasi . '"' . ($request->status_kurasi === 'rejected' ? ' dengan alasan: ' . $request->alasan_penolakan : ''),
        ]);

        return response()->json(['message' => 'Product status updated', 'product' => $product]);
    }

    /**
     * Fitur 1: Update tahapan inovasi — hanya admin yang memverifikasi produk ini
     */
    public function updateTahapan(Request $request, $id)
    {
        $request->validate([
            'id_tahapan' => 'required|exists:tahapan_inovasis,id',
        ]);

        $product = ProdukInovasi::with('tahapanInovasi')->findOrFail($id);
        $adminProfileId = $request->user()->adminProfile?->id;

        // Hanya admin yang memverifikasi produk ini yang bisa update tahapan
        if ($product->id_admin !== $adminProfileId) {
            return response()->json([
                'message' => 'Hanya admin yang memverifikasi inovasi ini yang dapat mengubah tahapan.'
            ], 403);
        }

        // Produk harus sudah disetujui
        if ($product->status_kurasi !== 'approved') {
            return response()->json([
                'message' => 'Tahapan hanya bisa diubah untuk produk yang sudah disetujui.'
            ], 400);
        }

        $oldTahapanName = $product->tahapanInovasi?->nama_tahapan;

        $product->update([
            'id_tahapan' => $request->id_tahapan,
        ]);

        $product->load('tahapanInovasi');
        $newTahapanName = $product->tahapanInovasi?->nama_tahapan;

        // Log action
        \App\Models\AdminLog::create([
            'id_admin' => $request->user()->id,
            'action' => 'update_tahapan',
            'target_id' => $product->id,
            'target_type' => 'product',
            'description' => 'Mengubah tahapan produk inovasi "' . $product->nama_inovasi . '" dari "' . ($oldTahapanName ?? '-') . '" menjadi "' . ($newTahapanName ?? '-') . '"',
        ]);

        return response()->json([
            'message' => 'Tahapan inovasi berhasil diperbarui.',
            'product' => $product
        ]);
    }

    public function toggleActive(Request $request, $id)
    {
        $product = ProdukInovasi::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);

        // Log action
        \App\Models\AdminLog::create([
            'id_admin' => $request->user()->id,
            'action' => 'toggle_product_active',
            'target_id' => $product->id,
            'target_type' => 'product',
            'description' => 'Mengubah status keaktifan produk inovasi "' . $product->nama_inovasi . '" menjadi ' . ($product->is_active ? 'Aktif' : 'Nonaktif'),
        ]);

        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json(['message' => "Produk berhasil $status", 'product' => $product]);
    }

    public function getProductDetail($id)
    {
        $product = ProdukInovasi::with(['inisiatorProfile', 'opd', 'bentukInovasi', 'tahapanInovasi'])->findOrFail($id);
        return response()->json($product);
    }

    public function getUsers()
    {
        $users = User::with(['inisiatorProfile', 'adminProfile'])->get();
        return response()->json($users);
    }

    /**
     * Fitur 5: Delete user — Akun tidak boleh dihapus, hanya boleh dinonaktifkan
     */
    public function deleteUser($id)
    {
        return response()->json([
            'message' => 'Akun pengguna tidak dapat dihapus. Silakan gunakan fitur nonaktifkan akun.'
        ], 400);
    }

    /**
     * Fitur 5: Toggle aktif/nonaktif user — cascade ke produk jika dinonaktifkan
     */
    public function toggleUserActive(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Jangan bisa nonaktifkan diri sendiri
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Anda tidak bisa menonaktifkan akun Anda sendiri.'], 400);
        }

        // Jangan bisa nonaktifkan superadmin terakhir
        if ($user->role === 'superadmin' && $user->is_active && User::where('role', 'superadmin')->where('is_active', true)->count() <= 1) {
            return response()->json(['message' => 'Tidak bisa menonaktifkan superadmin terakhir.'], 400);
        }

        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);

        // Jika dinonaktifkan dan user adalah inisiator, nonaktifkan semua produknya
        if (!$newStatus && $user->inisiatorProfile) {
            ProdukInovasi::where('id_inisiator', $user->inisiatorProfile->id)
                ->update(['is_active' => false]);
        }

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';

        // Log action
        \App\Models\AdminLog::create([
            'id_admin' => $request->user()->id,
            'action' => 'toggle_user_active',
            'target_id' => $user->id,
            'target_type' => 'user',
            'description' => 'Mengubah status keaktifan pengguna "' . ($user->name ?? $user->username) . '" (' . $user->role . ') menjadi ' . ($newStatus ? 'Aktif' : 'Nonaktif'),
        ]);

        return response()->json([
            'message' => "Pengguna berhasil $statusText.",
            'user' => $user
        ]);
    }

    /**
     * Get admin action logs with filtering
     */
    public function getLogs(Request $request)
    {
        $query = \App\Models\AdminLog::with(['admin.adminProfile']);

        if ($request->has('target_type')) {
            $query->where('target_type', $request->target_type);
        }

        if ($request->has('target_id')) {
            $query->where('target_id', $request->target_id);
        }

        $logs = $query->latest()->get();
        return response()->json($logs);
    }
}
