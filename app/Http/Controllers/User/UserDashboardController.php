<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index()
    {
        $alats = ['Wallace 6Ton', 'Fibro 2.5Ton', 'Exca PC 100'];
        $ujis  = ['Kuat Tekan Beton', 'Ekstraksi Aspal', 'Tarik Baja'];

        return view('user.dashboard', compact('alats', 'ujis'));
    }

}
