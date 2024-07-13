<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('checkconnection', [RegisteredUserController::class, 'checkConnection']);
Route::post('loginmobile',[RegisteredUserController::class, 'login']);
Route::get('jabatan',[RegisteredUserController::class, 'jabatan']);
Route::get('cabang',[RegisteredUserController::class, 'cabang']);
Route::get('wilayah',[RegisteredUserController::class, 'wilayah']);
Route::get('direksi',[RegisteredUserController::class, 'direksi']);
Route::get('supervisor',[RegisteredUserController::class, 'supervisor']);
Route::get('adminkas',[RegisteredUserController::class, 'adminkas']);
Route::get('accountofficer',[RegisteredUserController::class, 'accountofficer']);
Route::get('kepalacabang',[RegisteredUserController::class, 'kepalacabang']);
Route::post('registermobile', [RegisteredUserController::class, 'register']);
Route::get('surat-peringatan/gambar/{filename}', [RegisteredUserController::class, 'serveImage']);
Route::get('surat-peringatan/pdf/{filename}', [RegisteredUserController::class ,'servePdf']);

Route::middleware(['auth:api', 'auth.with.api.token:Direksi,Kepala Cabang,Supervisor,Admin Kas,Account Officer'])->group(function () {
    
    
    Route::get('nasabah', [RegisteredUserController::class, 'getNasabahSP']);
    Route::post('surat_peringatan', [RegisteredUserController::class, 'SuratPeringatan']); // ngirim surat  //
    
    


    Route::get('nasabah/{id}', [RegisteredUserController::class, 'show']); 
    Route::get('nasabahs', [RegisteredUserController::class, 'getNasabah']);
    Route::get('suratperingatan', [RegisteredUserController::class, 'getSuratPeringatan']);

    Route::get('usermobile',[RegisteredUserController::class, 'getUserDetails']);
    Route::post('updatePegawaiKepalaCabang', [RegisteredUserController::class, 'updateKepalaCabang']);
    Route::post('updatePegawaiSupervisor', [RegisteredUserController::class, 'updateSupervisor']);
    Route::post('updatePegawaiAdminKas', [RegisteredUserController::class, 'updateAdminKas']);
    Route::post('updatePegawaiAccountOfficer', [RegisteredUserController::class, 'updateAccountOfficer']);
    Route::post('logoutmobile', [RegisteredUserController::class, 'logout']);

});