<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Exports\OrdersMultiExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('admin'); // kamu sudah punya 'admin' di route group
    }

    /**
     * Admin index - daftar order
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $query = Order::with(['items.alat', 'user'])->orderBy('created_at', 'desc');

        if ($q = $request->get('q')) {
            $query->where(function($qq) use ($q) {
                $qq->where('order_id', 'like', "%{$q}%")
                   ->orWhere('customer_name', 'like', "%{$q}%");
            });
        }

        $orders = $query->paginate($perPage);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Helper: cari order berdasarkan id numeric atau order_id
     */
    protected function findOrder($id)
    {
        $q = Order::with(['items.alat', 'user']);
        if (is_numeric($id)) {
            return $q->where('id', $id)->orWhere('order_id', $id)->firstOrFail();
        }
        return $q->where('order_id', $id)->firstOrFail();
    }

    /**
     * Hitung durasi hari (hari termasuk start & end jika kedua tanggal ada)
     * Mengembalikan integer atau null jika tidak bisa dihitung.
     */
    protected function computeDurasiHari(Order $order)
    {
        if (!empty($order->test_start) && !empty($order->test_end)) {
            try {
                $start = Carbon::parse($order->test_start);
                $end = Carbon::parse($order->test_end);
                if ($end >= $start) {
                    return $start->diffInDays($end) + 1;
                }
            } catch (\Throwable $e) {
                Log::warning("Gagal parse tanggal untuk order {$order->id}: ".$e->getMessage());
            }
        }
        return null;
    }

    /**
     * Show detail order (admin)
     */
    public function show($id)
    {
        $order = $this->findOrder($id);
        $order->load(['items.alat', 'user']);
        $order->durasi_hari = $this->computeDurasiHari($order);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update status order (admin)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $allowed = ['UNPAID','PENDING','PAID','EXPIRED','CANCELLED'];
        $status = strtoupper($request->input('status'));

        if (!in_array($status, $allowed)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        DB::beginTransaction();
        try {
            $order = $this->findOrder($id);
            $prev = strtoupper($order->status ?? '');

            if ($status === 'PAID' && $prev !== 'PAID') {
                $order->paid_at = now();
            } elseif ($prev === 'PAID' && $status !== 'PAID') {
                $order->paid_at = null;
            }

            $order->status = $status;
            $order->save();

            // Lock/unlock alat sesuai status
            // Jika dibatalkan/expired/unpaid -> release alat
            if (in_array($status, ['CANCELLED','EXPIRED','UNPAID'])) {
                foreach ($order->items as $item) {
                    if (($item->type ?? '') === 'Sewa' && !empty($item->alat_id)) {
                        try {
                            $alat = $item->alat ?? \App\Models\AlatSewaType::find($item->alat_id);
                            if ($alat) {
                                $alat->is_locked = false;
                                $alat->save();
                            }
                        } catch (\Throwable $e) {
                            Log::warning("Gagal unlock alat {$item->alat_id}: ".$e->getMessage());
                        }
                    }
                }
            }

            // Jika PAID -> lock alat (opsional, tergantung business logic)
            if ($status === 'PAID') {
                foreach ($order->items as $item) {
                    if (($item->type ?? '') === 'Sewa' && !empty($item->alat_id)) {
                        try {
                            $alat = $item->alat ?? \App\Models\AlatSewaType::find($item->alat_id);
                            if ($alat) {
                                $alat->is_locked = true;
                                $alat->save();
                            }
                        } catch (\Throwable $e) {
                            Log::warning("Gagal lock alat {$item->alat_id}: ".$e->getMessage());
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', "Status berhasil diubah menjadi {$status}.");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal update order: '.$e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui order.');
        }
    }

    /**
     * Invoice view (admin)
     */
    public function invoice($id)
    {
        $order = $this->findOrder($id);
        $order->durasi_hari = $this->computeDurasiHari($order);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Cancel order (quick action via POST)
     */
    public function cancel(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $order = $this->findOrder($id);
            $order->status = 'CANCELLED';
            $order->save();

            // release alat jika perlu
            foreach ($order->items as $item) {
                if (($item->type ?? '') === 'Sewa' && !empty($item->alat_id)) {
                    try {
                        $alat = $item->alat ?? \App\Models\AlatSewaType::find($item->alat_id);
                        if ($alat) {
                            $alat->is_locked = false;
                            $alat->save();
                        }
                    } catch (\Throwable $e) {
                        Log::warning("Gagal unlock alat {$item->alat_id}: ".$e->getMessage());
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Order berhasil dibatalkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal cancel order: '.$e->getMessage());
            return redirect()->back()->with('error', 'Gagal membatalkan order.');
        }
    }

    public function invoicePrint(Order $order)
    {
        $order->load(['items.alat', 'user']);
        $order->durasi_hari = $this->computeDurasiHari($order);
        return view('admin.orders.invoice_print', compact('order'));
    }

    public function invoicePdf($idOrOrder)
    {
        if ($idOrOrder instanceof \App\Models\Order) {
            $order = $idOrOrder;

            if (empty($order->id)) {
                $routeId = request()->route('id') ?? request()->route('order') ?? null;
                if ($routeId) {
                    $order = $this->findOrder($routeId);
                } else {
                    $routeId = request()->input('id') ?? null;
                    if ($routeId) $order = $this->findOrder($routeId);
                }
            } else {
                $order->loadMissing(['items.alat', 'user']);
            }
        } else {
            $order = $this->findOrder($idOrOrder);
        }

        if (!$order || empty($order->id)) {
            abort(404, 'Order tidak ditemukan.');
        }

        $order->durasi_hari = $this->computeDurasiHari($order);

        $pdf = \PDF::loadView('admin.orders.invoice_pdf', compact('order'))
                ->setPaper('a4', 'portrait');

        return $pdf->stream('invoice-'.$order->order_id.'.pdf');
    }

    public function downloadFile(Order $order)
    {
        $this->authorize('view', $order);

        if (!$order->file_upload_path) {
            abort(404);
        }

        $path = storage_path('app/' . $order->file_upload_path); // jika file di storage/app/uploads/...
        if (!file_exists($path)) return abort(404);

        return response()->file($path); // browser akan render PDF/gambar jika bisa

    }

    public function edit($id)
    {
        $order = $this->findOrder($id);
        // durasi sudah dihitung di invoice() — tidak wajib di sini
        $order->durasi_hari = $this->computeDurasiHari($order);

        // render view form edit
        return view('admin.orders.edit', compact('order'));
    }

    public function editInvoice($id)
    {
        $order = $this->findOrder($id);
        $order->loadMissing(['items.alat', 'user']);
        $order->durasi_hari = $this->computeDurasiHari($order);
        return view('admin.orders.edit', compact('order'));
    }

    public function updateInvoice(Request $request, $id)
    {
        $order = $this->findOrder($id);

        $rules = [
            'provider_name' => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:255',
            'customer_contact' => 'nullable|string|max:50',
            'no_surat' => 'nullable|string|max:255',
            'tanggal_surat' => 'nullable|date',
            'alamat_pengirim' => 'nullable|string',
            'deskripsi_paket_pekerjaan' => 'nullable|string',
            'test_start' => 'nullable|date',   // ⬅️ tambahin validasi
            'test_end'   => 'nullable|date|after_or_equal:test_start', // ⬅️ tambahin validasi
            'file_upload' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'provider_logo' => 'nullable|image|max:5120',
        ];

        $data = $request->validate($rules);

        DB::beginTransaction();
        try {
            // assign field yang ada
            $order->provider_name = $data['provider_name'] ?? $order->provider_name;
            $order->customer_name = $data['customer_name'] ?? $order->customer_name;
            $order->customer_contact = $data['customer_contact'] ?? $order->customer_contact;
            $order->no_surat = $data['no_surat'] ?? $order->no_surat;

            if (!empty($data['tanggal_surat'])) {
                $order->tanggal_surat = Carbon::parse($data['tanggal_surat'])->toDateString();
            }

            $order->alamat_pengirim = $data['alamat_pengirim'] ?? $order->alamat_pengirim;
            $order->deskripsi_paket_pekerjaan = $data['deskripsi_paket_pekerjaan'] ?? $order->deskripsi_paket_pekerjaan;

            // ⬇️ Simpan tanggal pengujian
            if (!empty($data['test_start'])) {
                $order->test_start = Carbon::parse($data['test_start'])->toDateString();
            }
            if (!empty($data['test_end'])) {
                $order->test_end = Carbon::parse($data['test_end'])->toDateString();
            }

            // file_upload (opsional)
            if ($request->hasFile('file_upload')) {
                $file = $request->file('file_upload');
                $path = $file->store('uploads', 'public');
                if (!empty($order->file_upload_path) && Storage::disk('public')->exists($order->file_upload_path)) {
                    Storage::disk('public')->delete($order->file_upload_path);
                }
                $order->file_upload_path = $path;
            }

            // provider logo (opsional)
            if ($request->hasFile('provider_logo')) {
                $logo = $request->file('provider_logo');
                $logoPath = $logo->store('logos', 'public');
                if (!empty($order->provider_logo_path) && Storage::disk('public')->exists($order->provider_logo_path)) {
                    Storage::disk('public')->delete($order->provider_logo_path);
                }
                $order->provider_logo_path = $logoPath;
            }

            $order->save();
            DB::commit();

            return redirect()->route('admin.orders.invoice', $order->id)->with('success', 'Invoice berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal update invoice (admin): '.$e->getMessage(), ['order_id' => $order->id ?? null]);
            return redirect()->back()->withInput()->withErrors('Gagal menyimpan perubahan.');
        }
    }


    public function export(Request $request)
    {
        $q = $request->get('q');
        $status = $request->get('status');

        $startDate = $request->get('start_date'); // YYYY-MM-DD
        $endDate = $request->get('end_date');     // YYYY-MM-DD

        // whitelist kolom tanggal yang boleh dipakai
        $allowedDateFields = ['created_at', 'tanggal_masuk', 'test_start'];
        $dateField = $request->get('date_field', 'created_at');
        if (!in_array($dateField, $allowedDateFields)) {
            $dateField = 'created_at';
        }

        $query = Order::with(['items.alat', 'user'])->orderBy('created_at', 'desc');

        if (!empty($q)) {
            $query->where(function($qq) use ($q) {
                $qq->where('order_id', 'like', "%{$q}%")
                ->orWhere('customer_name', 'like', "%{$q}%")
                ->orWhereHas('user', function($q2) use ($q) {
                    $q2->where('name', 'like', "%{$q}%");
                })
                ->orWhereHas('items', function($q3) use ($q) {
                    $q3->where('name', 'like', "%{$q}%");
                });
            });
        }

        if (!empty($status) && strtolower($status) !== 'all') {
            $query->where('status', strtoupper($status));
        }

        // apply date range (inclusive) — gunakan whereDate agar cocok untuk date/datetime
        try {
            if (!empty($startDate) && !empty($endDate)) {
                $start = Carbon::parse($startDate)->toDateString();
                $end = Carbon::parse($endDate)->toDateString();
                $query->whereDate($dateField, '>=', $start)
                    ->whereDate($dateField, '<=', $end);
            } elseif (!empty($startDate)) {
                $start = Carbon::parse($startDate)->toDateString();
                $query->whereDate($dateField, '>=', $start);
            } elseif (!empty($endDate)) {
                $end = Carbon::parse($endDate)->toDateString();
                $query->whereDate($dateField, '<=', $end);
            }
        } catch (\Throwable $e) {
            Log::warning('Invalid date filter for export(): '.$e->getMessage(), compact('startDate','endDate','dateField'));
        }

        $orders = $query->get();

        // debug tip: jika ingin melihat apa yang diterima server, sementara:
        // Log::info('Export request payload', $request->all());

        $rangePart = '';
        if (!empty($startDate) || !empty($endDate)) {
            $s = $startDate ? Carbon::parse($startDate)->format('Ymd') : 'start';
            $e = $endDate ? Carbon::parse($endDate)->format('Ymd') : 'end';
            $rangePart = "-{$s}_{$e}";
        }

        $filename = 'orders' . $rangePart . '-' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new OrdersMultiExport($orders), $filename);
    }

    public function updateItem(Request $request, $id, $itemId)
    {
        $validated = $request->validate([
            'rental_start' => 'required|date',
            'rental_end'   => 'required|date|after_or_equal:rental_start',
        ]);

        // Cari order item
        $item = \DB::table('order_items')
            ->where('order_id', $id)
            ->where('id', $itemId)
            ->first();

        if (!$item) {
            return back()->with('error', 'Item tidak ditemukan.');
        }

        // Update
        \DB::table('order_items')
            ->where('id', $itemId)
            ->update([
                'rental_start' => $validated['rental_start'],
                'rental_end'   => $validated['rental_end'],
                'updated_at'   => now(),
            ]);

        return back()->with('success', 'Tanggal rental berhasil diperbarui.');
    }

}
