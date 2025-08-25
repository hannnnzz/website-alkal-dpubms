<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\AlatSewaType;
use App\Models\UjiType;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // statistik utama
        $totalOrder = Order::count();
        $totalAlatSewa = AlatSewaType::count();
        $totalUji = UjiType::count();
        $totalPendapatan = Order::where('status', 'PAID')->sum('amount');

        // allowed per-page values
        $allowedPer = [3, 10, 25, 50, 100];
        $perPage = (int) $request->query('per_page', 3);
        if (! in_array($perPage, $allowedPer)) {
            $perPage = 3;
        }

        // Server-side search & status (case-insensitive)
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', 'all')); // 'all' means no filtering

        // Map friendly status (lowercase) to DB values if needed
        $statusMap = [
            'paid'      => 'PAID',
            'unpaid'    => 'UNPAID',
            'pending'   => 'PENDING',
            'cancelled' => 'CANCELLED',
            'expired'   => 'EXPIRED',
        ];

        $query = Order::with(['user', 'items.alat']);

        // apply status filter if not 'all'
        if ($status !== 'all') {
            $statusLower = strtolower($status);
            if (isset($statusMap[$statusLower])) {
                $query->where('status', $statusMap[$statusLower]);
            } else {
                // if user passed an arbitrary status, try exact match (case-insensitive)
                $query->whereRaw('LOWER(status) = ?', [strtolower($status)]);
            }
        }

        // apply search across order_id, user name and item names
        if ($search !== '') {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                // order id
                $q->where('order_id', 'like', $like);

                // user name
                $q->orWhereHas('user', function ($u) use ($like) {
                    $u->where('name', 'like', $like);
                });

                // items' name
                $q->orWhereHas('items', function ($it) use ($like) {
                    $it->where('name', 'like', $like);
                });
            });
        }

        // ordering & paginate; withQueryString ensures per_page & search/status preserved in links
        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        return view('admin.dashboard', [
            'totalOrder' => $totalOrder,
            'totalAlatSewa' => $totalAlatSewa,
            'totalUji' => $totalUji,
            'totalPendapatan' => $totalPendapatan,
            'orders' => $orders,
            'perPage' => $perPage,
            'search' => $search,
            'status' => $status,
        ]);
    }
}
