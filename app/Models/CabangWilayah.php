<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CabangWilayah extends Pivot
{
    protected $table = 'cabang_wilayahs';

    protected $fillable = [
        'id_wilayah',
        'id_cabang',
    ];

    // Tidak ada timestamps di default
    public $timestamps = false;
}
