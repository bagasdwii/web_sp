<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direksi extends Model
{


   
    protected $table = 'direksis'; // Sesuaikan dengan nama tabel yang sesuai
    protected $primaryKey = 'id_direksi'; // Atur primary key jika perlu
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id'); // Foreign key 'id_user' di tabel 'direksi' yang merujuk ke 'id' di tabel 'users'
    }
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
