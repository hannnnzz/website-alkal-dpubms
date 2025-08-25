<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;

class OrdersMultiExport implements WithMultipleSheets
{
    protected $orders;

    public function __construct(Collection $orders)
    {
        $this->orders = $orders instanceof Collection ? $orders : collect($orders);
    }

    public function sheets(): array
    {
        $orders = $this->orders;

        // exact match case-insensitive untuk 'Alat Berat' dan 'Laboratorium'
        $alatBerat = $orders->filter(function($o){
            return isset($o->disposisi) && strcasecmp(trim($o->disposisi), 'Alat Berat') === 0;
        })->values();

        $laboratorium = $orders->filter(function($o){
            return isset($o->disposisi) && strcasecmp(trim($o->disposisi), 'Laboratorium') === 0;
        })->values();

        // sisanya (non-empty disposisi yang bukan kedua kategori di atas)
        $others = $orders->filter(function($o) {
            $d = isset($o->disposisi) ? trim($o->disposisi) : '';
            return $d !== '' && strcasecmp($d, 'Alat Berat') !== 0 && strcasecmp($d, 'Laboratorium') !== 0;
        })->values();

        $sheets = [];

        $sheets[] = new OrdersSheet($alatBerat, 'Alat Berat');
        $sheets[] = new OrdersSheet($laboratorium, 'Laboratorium');

        if ($others->isNotEmpty()) {
            $sheets[] = new OrdersSheet($others, 'Lainnya');
        }

        return $sheets;
    }
}
