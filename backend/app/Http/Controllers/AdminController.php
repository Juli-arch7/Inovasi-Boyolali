<?php

namespace App\Http\Controllers;

use App\Models\ProdukInovasi;
use App\Models\OPD;
use App\Models\User;
use App\Models\InisiatorProfile;
use App\Models\JenisInisiator;
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
        $perPage = $request->query('per_page', 10);
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');

        $query = ProdukInovasi::with([
            'inisiatorProfile.jenisInisiator',
            'bentukInovasi',
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
