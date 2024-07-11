<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;

class CabangController extends Controller
{
    public function index()
    {
        $cabangs = Cabang::all(); // Ambil semua data cabang dari model

        return view('cabang.index', compact('cabangs'));
    }
}
