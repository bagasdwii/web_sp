<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPeringatan extends Model
{
    use HasFactory;
    protected $fillable = [
        'no', 'tingkat', 'tanggal', 'keterangan', 'bukti_gambar', 'scan_pdf', 'id_account_officer'
    ];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class, 'no');
    }
}
