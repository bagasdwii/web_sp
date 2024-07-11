<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Direksi;
use App\Models\Jabatan;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use App\Models\PegawaiAdminKas;
use App\Models\PegawaiSupervisor;
use App\Models\PegawaiKepalaCabang;
use App\Http\Controllers\Controller;
use App\Models\PegawaiAccountOffice;

class RegisterMobileController extends Controller
{
    //
    public function jabatan()
    {
        $jabatan = Jabatan::all();
        return response()->json($jabatan);
    }
    public function cabang()
    {
        $cabang = Cabang::all();
        return response()->json($cabang);
    }
    public function wilayah()
    {
        $wilayah = Wilayah::all();
        return response()->json($wilayah);
    }
    public function direksi()
    {
        $direksi = Direksi::all();
        return response()->json($direksi);
    }
    public function kepalacabang()
    {
        $kepalacabang = PegawaiKepalaCabang::all();
        return response()->json($kepalacabang);
    }
    public function supervisor()
    {
        $supervisor = PegawaiSupervisor::all();
        return response()->json($supervisor);
    }
    public function adminkas()
    {
        $adminkas = PegawaiAdminKas::all();
        return response()->json($adminkas);
    }
    public function accountofficer()
    {
        $accountofficer = PegawaiAccountOffice::all();
        return response()->json($accountofficer);
    }
}
