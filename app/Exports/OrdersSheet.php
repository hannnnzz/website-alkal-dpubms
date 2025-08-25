<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class OrdersSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $orders;
    protected $title;

    public function __construct(Collection $orders, string $title = 'Sheet')
    {
        $this->orders = $orders instanceof Collection ? $orders : collect($orders);
        $this->title = $title;
    }

    public function collection()
    {
        return $this->orders->values();
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Provider Name',
            'Tanggal Dibuat',
            'Customer Name',
            'Customer Contact',
            'Items (nama|tipe|harga)',
            'Item Types',
            'Total Hari',
            'Status',
            'Paid At',
            'Tanggal Masuk',
            'Hari',
            'Tanggal Surat',
            'No Surat',
            'Alamat Pengirim',
            'Perihal',
            'Disposisi',
            'Deskripsi Paket Pekerjaan',
            'Test Start',
            'Test End',
        ];
    }

    protected function fmtDate($value, $format = 'Y-m-d H:i')
    {
        if (!$value) return '';
        try {
            return Carbon::parse($value)->format($format);
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }

    public function map($order): array
    {
        $itemsString = '';
        $itemTypes = '';

        try {
            $itemsString = $order->items->map(function($i){
                $name = $i->name ?? '';
                $type = $i->type ?? '';
                $price = isset($i->price) ? number_format($i->price, 0, ',', '.') : '0';
                return "{$name} ({$type}) - Rp{$price}";
            })->join(" ; ");

            $itemTypes = $order->items->pluck('type')
                                ->filter()
                                ->unique()
                                ->values()
                                ->join(', ');
        } catch (\Throwable $e) {
            $itemsString = '';
            $itemTypes = '';
        }

        return [
            $order->order_id ?? '',
            $order->provider_name ?? '',
            $this->fmtDate($order->created_at, 'Y-m-d H:i'),
            $order->customer_name ?? '',
            $order->customer_contact ?? '',
            $itemsString,
            $itemTypes,
            $order->amount ?? 0,
            $order->status ?? '',
            $this->fmtDate($order->paid_at, 'Y-m-d H:i'),
            $this->fmtDate($order->tanggal_masuk, 'Y-m-d'),
            $order->hari ?? '',
            $this->fmtDate($order->tanggal_surat, 'Y-m-d'),
            $order->no_surat ?? '',
            $order->alamat_pengirim ?? '',
            $order->perihal ?? '',
            $order->disposisi ?? '',
            $order->deskripsi_paket_pekerjaan ?? '',
            $this->fmtDate($order->test_start, 'Y-m-d'),
            $this->fmtDate($order->test_end, 'Y-m-d'),
        ];
    }

    public function title(): string
    {
        // Pastikan judul <= 31 karakter (Excel limit)
        return mb_substr($this->title, 0, 31);
    }
}
