<?php

namespace App\Http\Controllers;

use App\Models\ProdukInovasi;
use App\Models\OPD;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getStats()
    {
        $total      = ProdukInovasi::count();
        $approved   = ProdukInovasi::where('status_kurasi', 'approved')->count();
        $pending    = ProdukInovasi::where('status_kurasi', 'pending')->count();
        $rejected   = ProdukInovasi::where('status_kurasi', 'rejected')->count();
        $nonaktif   = ProdukInovasi::where('is_active', false)->count();
        $totalOpd   = OPD::count();
        $totalInisiator = User::where('role', 'inisiator')->count();

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
            'per_tahun' => $perTahun,
            'per_bulan' => $perBulan,
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

        return response()->json(['message' => 'Product status updated', 'product' => $product]);
    }

    public function toggleActive(Request $request, $id)
    {
        $product = ProdukInovasi::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);
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
