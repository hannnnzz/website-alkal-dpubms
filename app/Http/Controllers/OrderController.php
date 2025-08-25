<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\UjiType;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\AlatSewaType;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function createuji()
    {
        $ujis = UjiType::pluck('price', 'name'); // ['Nama Uji' => harga]
        return view('user.order.createuji', compact('ujis'));
    }

    public function createsewa()
    {
        $alats = AlatSewaType::all()->mapWithKeys(function ($alat) {
            return [
                $alat->name => [
                    'harga' => $alat->price,
                    'locked' => (bool) $alat->is_locked,
                ]
            ];
        });

        return view('user.order.createsewa', compact('alats'));
    }


    // STORE UJI
    public function storeuji(Request $req)
    {
        $req->validate([
            'provider_name'    => 'required|string',
            'customer_contact' => 'required|string',
            'ujis'             => 'required|array|min:1',
            'file_upload'      => 'nullable|file',
            'tanggal_masuk'    => 'nullable|date',
            'tanggal_surat'    => 'nullable|date',
            'no_surat'         => 'nullable|string',
            'alamat_pengirim'  => 'nullable|string',
            'perihal'          => 'nullable|string',
            'disposisi'        => 'nullable|in:Alat Berat,Laboratorium',
            'deskripsi_paket_pekerjaan' => 'nullable|string',
            'hari'             => 'nullable|string',
        ]);

        $path = $req->file('file_upload') ? $req->file('file_upload')->store('uploads', 'public') : null;

        DB::beginTransaction();
        try {
            $order = new Order();
            $order->user_id          = $req->user()->id;
            $order->order_id         = $this->generateInvoiceId('P');
            $order->provider_name    = $req->provider_name;
            $order->customer_name    = $req->user()->name;
            $order->customer_contact = $req->customer_contact;
            $order->file_upload_path = $path;
            $order->amount           = 0;
            $order->status           = 'UNPAID';

            // ====== Tanggal Masuk ======
            if ($req->filled('tanggal_masuk') && Schema::hasColumn('orders', 'tanggal_masuk')) {
                $order->tanggal_masuk = Carbon::parse($req->tanggal_masuk)->toDateTimeString();
            } elseif (Schema::hasColumn('orders', 'tanggal_masuk')) {
                $order->tanggal_masuk = Carbon::now()->toDateTimeString();
            }

            // ====== Kolom Administratif ======
            if ($req->filled('tanggal_surat') && Schema::hasColumn('orders', 'tanggal_surat')) {
                $order->tanggal_surat = Carbon::parse($req->tanggal_surat)->toDateString();
            }

            if ($req->filled('no_surat') && Schema::hasColumn('orders', 'no_surat')) {
                $order->no_surat = $req->no_surat;
            }

            if ($req->filled('alamat_pengirim') && Schema::hasColumn('orders', 'alamat_pengirim')) {
                $order->alamat_pengirim = $req->alamat_pengirim;
            }

            if ($req->filled('perihal') && Schema::hasColumn('orders', 'perihal')) {
                $order->perihal = $req->perihal;
            }

            if ($req->filled('disposisi') && Schema::hasColumn('orders', 'disposisi')) {
                $order->disposisi = $req->disposisi;
            } elseif (Schema::hasColumn('orders', 'disposisi')) {
                $order->disposisi = 'Laboratorium';
            }

            if ($req->filled('deskripsi_paket_pekerjaan') && Schema::hasColumn('orders', 'deskripsi_paket_pekerjaan')) {
                $order->deskripsi_paket_pekerjaan = $req->deskripsi_paket_pekerjaan;
            }

            // ====== Hari ======
            if (Schema::hasColumn('orders', 'hari')) {
                try {
                    if (!empty($order->tanggal_masuk)) {
                        $order->hari = Carbon::parse($order->tanggal_masuk)->locale('id')->isoFormat('dddd');
                    }
                } catch (\Exception $e) {
                    $order->hari = Carbon::parse($order->tanggal_masuk)->format('l');
                }
            }

            $order->save();

            // ====== Items Uji ======
            $ujiTypes = UjiType::whereIn('name', $req->ujis)->get();
            $total = 0;

            foreach ($ujiTypes as $uji) {
                $order->items()->create([
                    'type'         => 'Uji',
                    'name'         => $uji->name,
                    'quantity'     => 1,
                    'price'        => $uji->price,
                    'rental_start' => null, // admin isi nanti
                    'rental_end'   => null, // admin isi nanti
                ]);
                $total += $uji->price;
            }

            // ====== Update amount ======
            if (Schema::hasColumn('orders', 'amount')) {
                $order->update(['amount' => $total]);
            } else {
                $order->amount = $total;
                $order->save();
            }

            DB::commit();

            return redirect()->route('payment.qris', $order->id);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('storeuji error: '.$e->getMessage());
            return back()->withErrors('Terjadi kesalahan saat membuat pesanan UJI.');
        }
    }


    //STORE SEWA

    public function storesewa(Request $req)
    {
        $req->validate([
            'provider_name'    => 'required|string',
            'customer_contact' => 'required|string',
            'file_upload'      => 'nullable|file',
            'sewa'             => 'required|array|min:1',
            'sewa.*.alat'      => 'required|string',
            'sewa.*.tanggal'   => 'required|string', // "2025-08-10 - 2025-08-15"
        ]);

        $path = $req->file('file_upload') ? $req->file('file_upload')->store('uploads', 'public') : null;

        // debug log (opsional)
        try {
            Log::info('storesewa debug DB', [
                'db' => DB::connection()->getDatabaseName(),
                'has_test_date' => Schema::hasColumn('orders', 'test_date'),
                'has_test_start' => Schema::hasColumn('orders', 'test_start'),
                'has_test_end' => Schema::hasColumn('orders', 'test_end'),
            ]);
        } catch (\Throwable $e) {
            // ignore
        }

        DB::beginTransaction();
        try {
            $order = new Order();
            $order->user_id          = $req->user()->id;
            $order->order_id         = $this->generateInvoiceId('A');
            $order->provider_name    = $req->provider_name;
            $order->customer_name    = $req->user()->name;
            $order->customer_contact = $req->customer_contact;
            $order->file_upload_path = $path;
            $order->amount           = 0;
            $order->status           = 'UNPAID';

            // Ambil test_start/test_end dari sewa[0] (parse dengan Carbon)
            [$startFirstRaw, $endFirstRaw] = explode(' - ', $req->sewa[0]['tanggal']);
            $startFirst = Carbon::parse($startFirstRaw)->toDateString();
            $endFirst   = Carbon::parse($endFirstRaw)->toDateString();

            // UNTUK CASE SEWA: simpan ke test_start/test_end (jika kolom ada).
            // Jika test_start tidak ada tapi test_date ada di tabel, simpan ke test_date.
            // Jika keduanya tidak ada, fallback ke tanggal_masuk.
            if (Schema::hasColumn('orders', 'test_start')) {
                $order->test_start = $startFirst;
                if (Schema::hasColumn('orders', 'test_end')) {
                    $order->test_end = $endFirst;
                }
            } elseif (Schema::hasColumn('orders', 'test_date')) {
                // meskipun kamu bilang createsewa tidak punya test_date, ini fallback aman
                $order->test_date = $startFirst;
            } elseif (Schema::hasColumn('orders', 'tanggal_masuk')) {
                $order->tanggal_masuk = Carbon::parse($startFirst)->toDateTimeString();
            }

            // tanggal_masuk: jika dikirim oleh user, gunakan (jika kolom ada)
            if ($req->filled('tanggal_masuk') && Schema::hasColumn('orders', 'tanggal_masuk')) {
                $order->tanggal_masuk = Carbon::parse($req->tanggal_masuk)->toDateTimeString();
            } elseif (Schema::hasColumn('orders', 'tanggal_masuk') && empty($order->tanggal_masuk)) {
                // jika kolom ada tapi belum di-set, gunakan now
                $order->tanggal_masuk = Carbon::now()->toDateTimeString();
            }

            // optional administratif lain (cek column sebelum assignment)
            if ($req->filled('tanggal_surat') && Schema::hasColumn('orders', 'tanggal_surat')) {
                $order->tanggal_surat = Carbon::parse($req->tanggal_surat)->toDateString();
            } elseif (Schema::hasColumn('orders', 'tanggal_surat') && empty($order->tanggal_surat)) {
                $order->tanggal_surat = Carbon::now()->toDateString();
            }

            if ($req->filled('no_surat') && Schema::hasColumn('orders', 'no_surat')) {
                $order->no_surat = $req->no_surat;
            } elseif (Schema::hasColumn('orders', 'no_surat')) {
                $order->no_surat = $order->no_surat ?? '-';
            }

            if ($req->filled('alamat_pengirim') && Schema::hasColumn('orders', 'alamat_pengirim')) {
                $order->alamat_pengirim = $req->alamat_pengirim;
            } elseif (Schema::hasColumn('orders', 'alamat_pengirim')) {
                $order->alamat_pengirim = $order->alamat_pengirim ?? '-';
            }

            if ($req->filled('perihal') && Schema::hasColumn('orders', 'perihal')) {
                $order->perihal = $req->perihal;
            } elseif (Schema::hasColumn('orders', 'perihal')) {
                $order->perihal = $order->perihal ?? '-';
            }

            if ($req->filled('disposisi') && Schema::hasColumn('orders', 'disposisi')) {
                $order->disposisi = $req->disposisi;
            } elseif (Schema::hasColumn('orders', 'disposisi')) {
                $order->disposisi = $order->disposisi ?? Order::DISPOSISI[0];
            }

            if ($req->filled('deskripsi_paket_pekerjaan') && Schema::hasColumn('orders', 'deskripsi_paket_pekerjaan')) {
                $order->deskripsi_paket_pekerjaan = $req->deskripsi_paket_pekerjaan;
            } elseif (Schema::hasColumn('orders', 'deskripsi_paket_pekerjaan')) {
                $order->deskripsi_paket_pekerjaan = $order->deskripsi_paket_pekerjaan ?? '-';
            }

            // hari: generate jika kolom ada
            if (Schema::hasColumn('orders', 'hari')) {
                if ($req->filled('hari')) {
                    $order->hari = $req->hari;
                } else {
                    if (!empty($order->tanggal_masuk)) {
                        try {
                            $order->hari = Carbon::parse($order->tanggal_masuk)->locale('id')->isoFormat('dddd');
                        } catch (\Exception $e) {
                            $order->hari = Carbon::parse($order->tanggal_masuk)->format('l');
                        }
                    } else {
                        try {
                            $order->hari = Carbon::parse($startFirst)->locale('id')->isoFormat('dddd');
                        } catch (\Exception $e) {
                            $order->hari = Carbon::parse($startFirst)->format('l');
                        }
                    }
                }
            }

            $order->save();

            $total = 0;

            foreach ($req->sewa as $s) {
                $alatName = $s['alat'];

                // Ambil record alat dengan lockForUpdate untuk menghindari race
                $alat = AlatSewaType::where('name', $alatName)->lockForUpdate()->first();

                if (!$alat) {
                    DB::rollBack();
                    return back()->withErrors("Alat {$alatName} tidak ditemukan.");
                }

                // ===== START: pemeriksaan is_locked / locked_until =====
                if (!empty($alat->is_locked) && $alat->is_locked) {
                    DB::rollBack();
                    return back()->withErrors("Alat {$alat->name} sedang dinonaktifkan dan tidak dapat dipesan saat ini.");
                }

                if (!empty($alat->locked_until) && Carbon::parse($alat->locked_until)->isFuture()) {
                    DB::rollBack();
                    return back()->withErrors("Alat {$alat->name} dikunci sampai {$alat->locked_until} dan tidak tersedia sekarang.");
                }
                // ===== END: pemeriksaan is_locked / locked_until =====

                // parse tanggal untuk item ini
                [$startDateRaw, $endDateRaw] = explode(' - ', $s['tanggal']);
                $start = Carbon::parse($startDateRaw)->startOfDay();
                $end   = Carbon::parse($endDateRaw)->endOfDay();
                $durasi = $start->diffInDays($end) + 1;

                // ---- Sederhanakan cek overlap ----
                $overlap = OrderItem::where('alat_id', $alat->id)
                    ->whereHas('order', function($q) {
                        $q->whereIn('status', ['PENDING', 'PAID']);
                    })
                    ->whereDate('rental_start', '<=', $end->format('Y-m-d'))
                    ->whereDate('rental_end', '>=', $start->format('Y-m-d'))
                    ->exists();

                if ($overlap) {
                    DB::rollBack();
                    return back()->withErrors("Alat {$alat->name} sudah dipesan pada rentang tanggal tersebut.");
                }

                $price = $alat->price * $durasi;

                $order->items()->create([
                    'type' => 'Sewa',
                    'alat_id' => $alat->id,
                    'name' => $alat->name,
                    'quantity' => $durasi,
                    'rental_start' => $start->toDateString(),
                    'rental_end' => $end->toDateString(),
                    'price' => $price,
                ]);

                $total += $price;
            }

            // update amount (kolom ini umumnya ada)
            if (Schema::hasColumn('orders', 'amount')) {
                $order->update(['amount' => $total]);
            } else {
                $order->amount = $total;
                $order->save();
            }

            DB::commit();

            return redirect()->route('payment.qris', $order->id);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('storesewa error: '.$e->getMessage());
            return back()->withErrors('Terjadi kesalahan saat membuat pesanan.');
        }
    }

    private function generateInvoiceId(string $prefix): string
    {
        $now = Carbon::now();
        $year = $now->format('Y');
        $month = $now->format('m');

        $pattern = "{$prefix}/{$year}/{$month}-%";

        $last = Order::where('order_id', 'like', $pattern)
                    ->orderBy('id', 'desc')
                    ->value('order_id');

        $next = 1;
        if ($last) {
            if (preg_match('/-(\d+)$/', $last, $m)) {
                $next = intval($m[1]) + 1;
            }
        }

        $seq = str_pad($next, 4, '0', STR_PAD_LEFT);
        return "{$prefix}/{$year}/{$month}-{$seq}";
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);

        if (Auth::id() !== $order->user_id) {
            abort(403);
        }

        $order->loadMissing(['items', 'user']);
        return view('user.order.show', ['order' => $order]);
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            // Ambil semua pesanan untuk admin
            $orders = Order::with('items', 'user')->latest()->get();
            return view('admin.orders.index', compact('orders'));
        } else {
            // Ambil hanya pesanan milik user yang sedang login
            $orders = $user->orders()->with('items')->latest()->get();
            return view('user.order.index', compact('orders'));
        }
    }

    public function cancel(Order $order)
    {
        // pastikan authorization: hanya owner atau admin
        if ($order->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        if (in_array($order->status, ['PAID','CANCELLED','EXPIRED'])) {
            return back()->withErrors('Order tidak bisa dibatalkan.');
        }

        DB::transaction(function() use ($order) {
            foreach ($order->items()->where('type','Sewa')->get() as $item) {
                if ($item->alat_id) {
                    $alat = AlatSewaType::find($item->alat_id);
                    if ($alat) $alat->update(['is_locked' => false, 'locked_until' => null]);
                }
            }
            $order->update(['status' => 'CANCELLED']);
        });

        return back()->with('success','Pesanan dibatalkan dan alat dilepas.');
    }

    public function getBookedDates($alat)
    {
        // $alat bisa berupa id (numeric) atau name
        $alatModel = is_numeric($alat)
            ? AlatSewaType::find($alat)
            : AlatSewaType::where('name', $alat)->first();

        if (!$alatModel) {
            return response()->json([]);
        }

        $items = OrderItem::where('alat_id', $alatModel->id)
            ->whereHas('order', function($q) {
                // hanya ambil order yang aktif (sesuaikan bila ada status lain)
                $q->whereIn('status', ['PENDING', 'PAID']);
            })
            ->get(['id','rental_start', 'rental_end']);

        if ($items->isEmpty()) {
            return response()->json([]);
        }

        $dates = [];

        foreach ($items as $item) {
            if (empty($item->rental_start) || empty($item->rental_end)) {
                continue;
            }

            try {
                $s = Carbon::parse($item->rental_start);
                $e = Carbon::parse($item->rental_end);

                // jika terbalik, swap supaya loop aman
                if ($e->lt($s)) {
                    [$s, $e] = [$e, $s];
                }

                for ($d = $s->copy(); $d->lte($e); $d->addDay()) {
                    $dates[] = $d->format('Y-m-d');
                }
            } catch (\Throwable $ex) {
                Log::debug('getBookedDates: parse failed', [
                    'order_item_id' => $item->id ?? null,
                    'rental_start' => $item->rental_start,
                    'rental_end' => $item->rental_end,
                    'err' => $ex->getMessage()
                ]);
                // lanjut ke item berikutnya
                continue;
            }
        }

        // unikkan & urutkan
        $dates = array_values(array_unique($dates));
        sort($dates);

        return response()->json($dates);
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'alat' => 'required|string', // nama alat
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $alat = AlatSewaType::where('name', $request->alat)->first();
        if (!$alat) {
            return response()->json(['available' => false, 'message' => 'Alat tidak ditemukan'], 404);
        }

        $start = Carbon::parse($request->start)->format('Y-m-d');
        $end   = Carbon::parse($request->end)->format('Y-m-d');

        $overlap = OrderItem::where('alat_id', $alat->id)
            ->whereHas('order', function ($q) {
                $q->whereIn('status', ['PENDING', 'PAID']);
            })
            ->whereDate('rental_start', '<=', $end)
            ->whereDate('rental_end', '>=', $start)
            ->exists();

        return response()->json([
            'available' => !$overlap
        ]);
    }

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

    protected function findOrder($id)
    {
        $q = Order::with(['items.alat', 'user']);
        if (is_numeric($id)) {
            return $q->where('id', $id)->orWhere('order_id', $id)->firstOrFail();
        }
        return $q->where('order_id', $id)->firstOrFail();
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

        $pdf = \PDF::loadView('user.order.invoice_pdf', compact('order'))
                ->setPaper('a4', 'portrait');

        return $pdf->stream('invoice-'.$order->order_id.'.pdf');
    }

}
