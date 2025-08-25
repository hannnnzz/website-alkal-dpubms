<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'type',
        'alat_id',
        'name',
        'quantity',
        'rental_start',
        'rental_end',
        'price',
    ];

    protected $casts = [
        'rental_start' => 'date',
        'rental_end' => 'date',
    ];

    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class, 'order_id', 'id');
    }

    public function alat()
    {
        return $this->belongsTo(\App\Models\AlatSewaType::class, 'alat_id', 'id');
    }
}
