<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    /**
     * Gunakan order_id untuk route-model binding
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Konstanta referensi
     */
    public const DISPOSISI = [
        'Alat Berat',
        'Laboratorium',
    ];

    public const STATUS = [
        'UNPAID',
        'PENDING',
        'PAID',
        'CANCELLED',
        'EXPIRED',
    ];

    /**
     * Mass assignable
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'provider_name',
        'customer_name',
        'customer_contact',
        'test_date',
        'test_start',
        'test_end',
        'file_upload_path',
        'amount',
        'status',
        'paid_at',
        'tanggal_masuk',
        'hari',
        'tanggal_surat',
        'no_surat',
        'alamat_pengirim',
        'perihal',
        'disposisi',
        'deskripsi_paket_pekerjaan',
    ];

    // Jika kamu lebih suka simpler selama development, pakai ini (pilih satu pendekatan):
    // protected $guarded = [];

    /**
     * Casts untuk tanggal / waktu
     */
    protected $casts = [
        'test_date' => 'date',
        'test_start' => 'date',
        'test_end' => 'date',
        'tanggal_masuk' => 'datetime',
        'tanggal_surat' => 'date',
        'paid_at' => 'datetime',
    ];

    /**
     * Booted: isi tanggal_masuk & hari otomatis
     */
    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->tanggal_masuk)) {
                $order->tanggal_masuk = now();
            }

            if (!empty($order->tanggal_masuk)) {
                try {
                    // Jika lingkungan mendukung locale 'id', isoFormat akan mereturn nama hari dlm bahasa Indonesia
                    $dt = Carbon::parse($order->tanggal_masuk)->locale('id');
                    $order->hari = $dt->isoFormat('dddd');
                } catch (\Exception $e) {
                    // fallback: nama hari bahasa Inggris
                    try {
                        $order->hari = Carbon::parse($order->tanggal_masuk)->format('l');
                    } catch (\Exception $e2) {
                        $order->hari = null;
                    }
                }
            }
        });

        static::saving(function ($order) {
            if ($order->isDirty('tanggal_masuk') && !empty($order->tanggal_masuk)) {
                try {
                    $dt = Carbon::parse($order->tanggal_masuk)->locale('id');
                    $order->hari = $dt->isoFormat('dddd');
                } catch (\Exception $e) {
                    try {
                        $order->hari = Carbon::parse($order->tanggal_masuk)->format('l');
                    } catch (\Exception $e2) {
                        // biarkan sebelumnya atau null
                    }
                }
            }
        });
    }

    public function items()
    {
        return $this->hasMany(\App\Models\OrderItem::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDisposisiLabelAttribute()
    {
        return $this->disposisi;
    }
}
