<?php

namespace App\Models;

use App\Models\Cabang;
use App\Models\Wilayah;
use App\Models\PegawaiKepalaCabang;
use Illuminate\Database\Eloquent\Model;

class PegawaiSupervisor extends Model
{
    protected $table = 'pegawai_supervisors'; // Sesuaikan dengan nama tabel yang sesuai
    protected $primaryKey = 'id_supervisor'; // Atur primary key jika perlu


    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang');
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'id_wilayah');
    }

    public function kepalaCabang()
    {
        return $this->belongsTo(PegawaiKepalaCabang::class, 'id_kepala_cabang');
    }


    protected $fillable = [
        'id_supervisor',
        'nama_supervisor',
        'id_user',
        'id_kepala_cabang',
        'id_jabatan',
        'id_cabang',
        'id_wilayah',
        'email',
        'password',
        // tambahkan atribut tambahan jika diperlukan
    ];
  
    // Tambahkan relasi atau metode lain jika diperlukan
}
