<?php

namespace App\Models;

use App\Models\Cabang;
use App\Models\Wilayah;
use App\Models\PegawaiSupervisor;
use Illuminate\Database\Eloquent\Model;

class PegawaiAdminKas extends Model
{
    protected $table = 'pegawai_admin_kas'; // Sesuaikan dengan nama tabel yang sesuai
    protected $primaryKey = 'id_admin_kas'; // Atur primary key jika perlu
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang');
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'id_wilayah');
    }

    public function supervisor()
    {
        return $this->belongsTo(PegawaiSupervisor::class, 'id_supervisor');
    }

    protected $fillable = [
        'id_admin_kas',
        'nama_admin_kas',
        'id_user',

        'id_supervisor',
        'id_jabatan',
        'id_cabang',
        'id_wilayah',
        'email',
        'password',
        // tambahkan atribut tambahan jika diperlukan
    ];

    // Tambahkan relasi atau metode lain jika diperlukan
}
