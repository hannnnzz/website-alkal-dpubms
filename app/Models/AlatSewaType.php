<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatSewaType extends Model
{
    protected $fillable = ['name', 'price', 'is_locked', 'locked_until'];

    protected $casts = [
        'is_locked' => 'boolean',
        'locked_until' => 'datetime',
    ];

}

