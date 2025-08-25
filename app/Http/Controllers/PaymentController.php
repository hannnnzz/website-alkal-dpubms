<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\CoreApi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        Config::$isSanitized  = true;
        Config::$is3ds        = false;
    }

    public function qris($id)
    {
        $order = Order::findOrFail($id);

        // authorisasi: owner atau admin
        if ($order->user_id !== auth()->id() && !(auth()->user() && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())) {
            abort(403);
        }

        $status = strtoupper(trim($order->status ?? ''));

        $qrImageUrl = null;
        $qrImageIsExternal = false;
        $qrImagePath = $order->qr_image_path ?? null;

        $fileExists = false;
        if (!empty($qrImagePath)) {
            try {
                $fileExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($qrImagePath);
            } catch (\Throwable $e) {
                \Log::warning('payment.qris storage exists check failed', ['err' => $e->getMessage(), 'path' => $qrImagePath]);
                $fileExists = false;
            }
        }

        if (in_array($status, ['UNPAID', 'PENDING'])) {
            if ($fileExists) {
                try {
                    $qrImageUrl = \Illuminate\Support\Facades\Storage::url($qrImagePath);
                    $qrImageIsExternal = false;
                } catch (\Throwable $e) {
                    \Log::warning('payment.qris Storage::url failed', ['err' => $e->getMessage(), 'path' => $qrImagePath]);
                    $qrImageUrl = null;
                }
            } elseif (!empty($order->payment_url)) {
                $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($order->payment_url);
                $qrImageIsExternal = true;
            }
        }

        \Log::info('payment.qris debug view', [
            'order_id' => $order->id,
            'order_order_id' => $order->order_id,
            'status' => $order->status,
            'status_upper' => $status,
            'qr_image_path_db' => $qrImagePath,
            'qr_image_file_exists' => $fileExists,
            'qr_image_url_to_view' => $qrImageUrl,
            'payment_url' => $order->payment_url ?? null,
        ]);

        return view('payment.qris', [
            'order' => $order,
            'qrUrl' => $order->payment_url ?? null,
            'qrImage' => $qrImageUrl,
            'qrImageIsExternal' => $qrImageIsExternal,
        ]);
    }



    public function checkout()
    {
        return view('checkout');
    }

    public function createPayment(Request $req)
    {
        $orderId = $this->generateInvoiceId('P');
        $amount  = (int) $req->amount;

        $order = Order::create([
            'user_id'             => Auth::id(),
            'order_id'            => $orderId,
            'amount'              => $amount,
            'customer_name'       => $req->name ?? 'Customer',
            'customer_contact'    => $req->phone ?? '08123456789',
            'status'              => 'UNPAID'
        ]);

        return redirect()->route('payment.qris', ['order' => $order->id]);
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

    public function generateQris(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Authorization
        if ($order->user_id !== auth()->id() && !(auth()->user() && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $status = strtoupper(trim($order->status ?? ''));
        if (!in_array($status, ['UNPAID', 'PENDING'])) {
            return response()->json(['success' => false, 'message' => 'Order sudah dibayar/expired'], 400);
        }

        // jika sudah PENDING dan file ada, return existing
        if ($status === 'PENDING' && !empty($order->qr_image_path) && \Storage::disk('public')->exists($order->qr_image_path)) {
            $publicUrl = \Storage::url($order->qr_image_path);
            return response()->json(['success' => true, 'qrImage' => $order->qr_image_path, 'qrImageUrl' => $publicUrl]);
        }

        try {
            // Build params to Midtrans
            $params = [
                'payment_type' => 'qris',
                'transaction_details' => [
                    'order_id'     => $order->order_id,
                    'gross_amount' => (int) $order->amount,
                ],
                'item_details' => [
                    [
                        'id'       => 'order-' . $order->id,
                        'price'    => (int) $order->amount,
                        'quantity' => 1,
                        'name'     => 'Total Pembayaran',
                    ]
                ],
                'customer_details' => [
                    'first_name' => $order->customer_name ?? 'Pelanggan',
                    'email'      => 'placeholder@example.com',
                    'phone'      => $order->customer_contact ?? '081234567890',
                ]
            ];

            // charge ke Midtrans
            $qrisResp = \Midtrans\CoreApi::charge($params);

            // LOG raw response (json-serializable)
            try {
                \Log::info('generateQris midtrans raw', ['order_id' => $order->id, 'resp' => json_decode(json_encode($qrisResp), true)]);
            } catch (\Throwable $e) {
                \Log::info('generateQris midtrans raw (non-jsonable)', ['order_id' => $order->id, 'err' => $e->getMessage()]);
            }

            // ambil QR url dari response (tolerant terhadap variasi struktur)
            $qrUrl = null;
            if (!empty($qrisResp->actions) && is_array($qrisResp->actions)) {
                foreach ($qrisResp->actions as $act) {
                    if (!empty($act->url)) {
                        $qrUrl = $act->url;
                        break;
                    }
                }
            }
            if (empty($qrUrl) && !empty($qrisResp->payment_url)) {
                $qrUrl = $qrisResp->payment_url;
            }
            if (empty($qrUrl) && is_object($qrisResp)) {
                // coba property lain
                $qrUrl = $qrisResp->redirect_url ?? $qrUrl;
            }

            if (empty($qrUrl)) {
                \Log::error('generateQris: no qrUrl found in midtrans response', ['order_id' => $order->id]);
                return response()->json(['success' => false, 'message' => 'Gagal mendapatkan URL QRIS dari Midtrans'], 500);
            }

            \Log::info('generateQris got qrUrl', ['order_id' => $order->id, 'qrUrl' => $qrUrl]);

            // generate image via qrserver (bisa heavy; timeout 15s)
            $qrImageApi = 'https://api.qrserver.com/v1/create-qr-code/?size=600x600&data=' . urlencode($qrUrl);
            $resp = \Illuminate\Support\Facades\Http::timeout(15)->get($qrImageApi);

            \Log::info('generateQris qrserver status', ['order_id' => $order->id, 'status' => $resp->status()]);

            if (!$resp->successful() || empty($resp->body())) {
                \Log::warning('generateQris qrserver failed or empty body', ['order_id' => $order->id, 'status' => $resp->status()]);
                $order->status = 'PENDING';
                $order->save();

                return response()->json([
                    'success' => true,
                    'qrImage' => null,
                    'qrImageUrl' => $qrImageApi,
                    'note' => 'Payment URL saved; QR image not stored (qrserver failed)'
                ]);
            }

            // simpan content ke storage public
            $content = $resp->body();
            $filename = 'qris/qris-' . $order->id . '-' . time() . '.png';

            $saved = \Storage::disk('public')->put($filename, $content);
            \Log::info('generateQris put result', ['order' => $order->id, 'filename' => $filename, 'saved' => $saved]);

            if (!$saved) {
                \Log::error('generateQris: failed to save file to disk', ['order' => $order->id, 'filename' => $filename]);
                // simpan payment_url & set status agar fallback tetap jalan
                $order->status = 'PENDING';
                $order->save();

                return response()->json(['success' => true, 'qrImage' => null, 'qrImageUrl' => $qrImageApi, 'note' => 'payment_url saved, image not stored']);
            }

            // **penting**: simpan ke DB menggunakan assignment + save (hindari $fillable)
            $order->qr_image_path = $filename;
            $order->status = 'PENDING';

            $savedOrder = $order->save();
            \Log::info('generateQris saved order', ['order_id' => $order->id, 'saved' => $savedOrder, 'qr_image_path' => $order->qr_image_path]);

            $publicUrl = \Storage::url($filename);

            return response()->json(['success' => true, 'qrImage' => $filename, 'qrImageUrl' => $publicUrl]);
        } catch (\Exception $e) {
            \Log::error('generateQris exception', ['order_id' => $order->id, 'err' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error generate QRIS: ' . $e->getMessage()], 500);
        }
    }

    public function webhook(Request $req)
    {
        $notif = $req->all();
        $order = Order::where('order_id', $notif['order_id'] ?? '')->first();

        if ($order && $notif['transaction_status'] === 'settlement') {
            $order->update([
                'status'  => 'PAID',
                'paid_at' => now(),
            ]);
        } elseif ($order && $notif['transaction_status'] === 'expire') {
            $order->update([
                'status' => 'EXPIRED',
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function success()
    {
        return view('success');
    }
}
