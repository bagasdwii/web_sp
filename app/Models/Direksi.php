<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direksi extends Model
{
    protected $table = 'direksis'; // Sesuaikan dengan nama tabel yang sesuai
    protected $primaryKey = 'id_direksi'; // Atur primary key jika perlu

    protected $fillable = [
        'id_direksi',
        'id_user',

        'nama',
        'email',
        'password',
        // tambahkan atribut tambahan jika diperlukan
    ];

    // Tambahkan relasi atau metode lain jika diperlukan
}
