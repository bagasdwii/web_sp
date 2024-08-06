<?php

namespace App\Models;

use App\Models\Cabang;
use App\Models\Wilayah;
use App\Models\PegawaiAdminKas;
use Illuminate\Database\Eloquent\Model;

class PegawaiAccountOffice extends Model
{
    protected $table = 'pegawai_account_offices'; // Sesuaikan dengan nama tabel yang sesuai
    protected $primaryKey = 'id_account_officer'; // Atur primary key jika perlu
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang');
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'id_wilayah');
    }

    public function adminKas()
    {
        return $this->belongsTo(PegawaiAdminKas::class, 'id_admin_kas');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id'); // Foreign key 'id_user' di tabel 'direksi' yang merujuk ke 'id' di tabel 'users'
    }

    protected $fillable = [
        'id_account_officer',
        'nama_account_officer',
        'id_user',

        'id_admin_kas',
        'id_jabatan',
        'id_cabang',
        'id_wilayah',
        'email',
        'password',
        // tambahkan atribut tambahan jika diperlukan
    ];

    // Tambahkan relasi atau metode lain jika diperlukan
}
