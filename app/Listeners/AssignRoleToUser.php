<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Direksi;
use App\Models\PegawaiAdminKas;
use App\Models\PegawaiSupervisor;
use App\Models\PegawaiKepalaCabang;
use Illuminate\Support\Facades\Log;
use App\Events\UserRegisteredMobile;
use App\Models\PegawaiAccountOffice;

class AssignRoleToUser
{
    /**
     * Handle the event.
     */
    public function handle(UserRegisteredMobile $event): void
    {
        // Eager load jabatan dan nip relations
        $user = User::with(['jabatan', 'nip', 'pegawaiKepalaCabang', 'pegawaiSupervisor', 'pegawaiAdminKas','pegawaiAccountOfficer', 'wilayah'])->find($event->user->id);
        Log::info('AssignRoleToUser listener invoked', ['user' => $user]);
    
        if ($user->jabatan_id == '2') {
            Log::info('Creating PegawaiKepalaCabang record');
            PegawaiKepalaCabang::create([
                'nama_kepala_cabang' => $user->name,
                'id_user' => $user->id,
                'id_jabatan' => $user->jabatan_id,
                'id_cabang' => $user->id_cabang ?? null,
                'id_direksi' => $user->id_direksi ?? null,
            ]);
            return; 
        } else if ($user->jabatan_id == '1') {
            Log::info('Creating Direksi record');
            Direksi::create([
                'nama' => $user->name,
                'id_user' => $user->id,
            ]);
            return; 
        } else if ($user->jabatan_id == '3') {
            Log::info('Creating PegawaiSupervisor record');
            PegawaiSupervisor::create([
                'nama_supervisor' => $user->name,
                'id_user' => $user->id,
                'id_kepala_cabang' => $user->id_kepala_cabang ?? null,
                'id_jabatan' => $user->jabatan_id,
                'id_cabang' => $user->id_cabang ?? null,
                'id_wilayah' => $user->id_wilayah ?? null,
            ]);
            return; 
        } else if ($user->jabatan_id == '4') {
            Log::info('Creating PegawaiAdminKas record');
            PegawaiAdminKas::create([
                'nama_admin_kas' => $user->name,
                'id_user' => $user->id,
                'id_supervisor' => $user->id_supervisor ?? null,
                'id_jabatan' => $user->jabatan_id,
                'id_cabang' => $user->id_cabang ?? null,
                'id_wilayah' => $user->id_wilayah ?? null,
            ]);
            return; 
        } else {
            Log::info('Creating PegawaiAccountOffice record');
            PegawaiAccountOffice::create([
                'nama_account_officer' => $user->name,
                'id_user' => $user->id,
                'id_admin_kas' => $user->id_admin_kas ?? null,
                'id_jabatan' => $user->jabatan_id,
                'id_cabang' => $user->id_cabang ?? null,
                'id_wilayah' => $user->id_wilayah ?? null,
            ]);
            return; 
        }
    }
    
}
