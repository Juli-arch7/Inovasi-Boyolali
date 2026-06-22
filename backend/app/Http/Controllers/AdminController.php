<?php

namespace App\Http\Controllers;

use App\Models\ProdukInovasi;
use App\Models\OPD;
use App\Models\User;
use App\Models\InisiatorProfile;
use App\Models\JenisInisiator;
use App\Models\TahapanInovasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $product = ProdukInovasi::with([
            'inisiatorProfile', 'opd', 'bentukInovasi', 'tahapanInovasi', 'mediaInovasi', 'adminProfile'
        ])->findOrFail($id);
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

    /**
     * Dashboard Statistics: total_inovasi, unit_kerja, masyarakat
     */
    public function dashboardStatistics(Request $request)
    {
        $year = $request->query('year');

        // Base query
        $baseQuery = ProdukInovasi::query();
        if ($year) {
            $baseQuery->where('tahun_inovasi', $year);
        }

        $total = (clone $baseQuery)->count();

        // Masyarakat: inovasi dari inisiator dengan jenis_inisiator = 'Masyarakat'
        $masyarakatJenisId = JenisInisiator::where('nama_jenis_inisiator', 'Masyarakat')->value('id');

        $masyarakat = 0;
        if ($masyarakatJenisId) {
            $masyarakatQuery = (clone $baseQuery)
                ->whereHas('inisiatorProfile', function ($q) use ($masyarakatJenisId) {
                    $q->where('id_jenis_inisiator', $masyarakatJenisId);
                });
            $masyarakat = $masyarakatQuery->count();
        }

        // Unit Kerja = total - masyarakat (otomatis tangkap jenis baru)
        $unitKerja = $total - $masyarakat;

        return response()->json([
            'total_inovasi' => $total,
            'unit_kerja' => $unitKerja,
            'masyarakat' => $masyarakat,
        ]);
    }

    /**
     * Dashboard Chart: per-tahun or per-bulan data
     */
    public function dashboardChart(Request $request)
    {
        $year = $request->query('year');

        if ($year) {
            // Per-bulan untuk tahun tertentu
            $labels = [];
            $data = [];
            $bulanNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];

            for ($m = 1; $m <= 12; $m++) {
                $labels[] = $bulanNames[$m - 1];
                $data[] = ProdukInovasi::where('tahun_inovasi', $year)
                    ->whereMonth('created_at', $m)
                    ->count();
            }

            return response()->json([
                'labels' => $labels,
                'data' => $data,
                'type' => 'monthly',
            ]);
        } else {
            // Per-tahun: semua tahun yang ada di database
            $results = ProdukInovasi::selectRaw('tahun_inovasi, COUNT(*) as total')
                ->groupBy('tahun_inovasi')
                ->orderBy('tahun_inovasi', 'asc')
                ->get();

            return response()->json([
                'labels' => $results->pluck('tahun_inovasi')->map(fn($y) => (string) $y)->toArray(),
                'data' => $results->pluck('total')->toArray(),
                'type' => 'yearly',
            ]);
        }
    }

    /**
     * Dashboard Inovasi List: filtered, searched, paginated, sorted
     */
    public function dashboardInovasi(Request $request)
    {
        $filter = $request->query('filter', 'all'); // all, unit_kerja, masyarakat
        $year = $request->query('year');
        $search = $request->query('search');
        $opd = $request->query('opd');
        $status = $request->query('status');
        $perPage = $request->query('per_page', 10);
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');

        $query = ProdukInovasi::with([
            'inisiatorProfile.jenisInisiator',
            'bentukInovasi',
            'tahapanInovasi',
            'opd',
        ]);

        // Filter tahun
        if ($year) {
            $query->where('tahun_inovasi', $year);
        }

        // Filter jenis inisiator
        if ($filter !== 'all') {
            $masyarakatJenisId = JenisInisiator::where('nama_jenis_inisiator', 'Masyarakat')->value('id');

            if ($filter === 'masyarakat' && $masyarakatJenisId) {
                $query->whereHas('inisiatorProfile', function ($q) use ($masyarakatJenisId) {
                    $q->where('id_jenis_inisiator', $masyarakatJenisId);
                });
            } elseif ($filter === 'unit_kerja' && $masyarakatJenisId) {
                $query->whereHas('inisiatorProfile', function ($q) use ($masyarakatJenisId) {
                    $q->where('id_jenis_inisiator', '!=', $masyarakatJenisId);
                });
            }
        }

        // Filter OPD
        if ($opd) {
            if (is_numeric($opd)) {
                $query->where('id_opd', $opd);
            } else {
                $query->whereHas('opd', function ($q) use ($opd) {
                    $q->where('nama_opd', $opd);
                });
            }
        }

        // Filter Status
        if ($status) {
            if ($status === 'active') {
                $query->where('is_active', true)->where('status_kurasi', 'approved');
            } elseif ($status === 'inactive') {
                $query->where('is_active', false)->where('status_kurasi', 'approved');
            } elseif (in_array($status, ['pending', 'approved', 'rejected'])) {
                $query->where('status_kurasi', $status);
            }
        }

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_inovasi', 'like', "%{$search}%")
                  ->orWhereHas('inisiatorProfile', function ($q2) use ($search) {
                      $q2->where('nama_inisiator', 'like', "%{$search}%");
                  })
                  ->orWhereHas('bentukInovasi', function ($q2) use ($search) {
                      $q2->where('nama_bentuk', 'like', "%{$search}%");
                  });
            });
        }

        // Sorting
        $allowedSorts = ['nama_inovasi', 'tahun_inovasi', 'status_kurasi', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $paginated = $query->paginate($perPage);

        // Transform data
        $items = collect($paginated->items())->map(function ($p) {
            return [
                'id' => $p->id,
                'nama_inovasi' => $p->nama_inovasi,
                'jenis_inisiator' => $p->inisiatorProfile?->jenisInisiator?->nama_jenis_inisiator ?? '-',
                'nama_inisiator' => $p->inisiatorProfile?->nama_inisiator ?? '-',
                'jenis_inovasi' => $p->bentukInovasi?->nama_bentuk ?? '-',
                'nama_opd' => $p->opd?->nama_opd ?? '-',
                'nama_tahapan' => $p->tahapanInovasi?->nama_tahapan ?? '-',
                'tahun_inovasi' => $p->tahun_inovasi,
                'status_kurasi' => $p->status_kurasi,
                'is_active' => $p->is_active,
                'created_at' => $p->created_at?->format('d M Y'),
            ];
        });

        return response()->json([
            'data' => $items,
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
        ]);
    }
}
