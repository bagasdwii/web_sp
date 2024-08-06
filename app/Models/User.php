<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Key;
use App\Models\Nip;
use App\Models\Status;
use App\Models\Direksi;
use App\Models\Jabatan;
use App\Models\Wilayah;
use App\Models\PegawaiAdminKas;
use App\Models\PegawaiSupervisor;
use Laravel\Sanctum\HasApiTokens;
use App\Models\PegawaiKepalaCabang;
use App\Events\UserRegisteredMobile;
use App\Models\PegawaiAccountOffice;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $primaryKey = 'id';
    
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }
    public function direksi()
    {
        return $this->hasOne(Direksi::class, 'id_user');
    }

    // public function nip()
    // {
    //     return $this->belongsTo(Nip::class, 'nip', 'nip');
    // }
    public function key()
    {
        return $this->belongsTo(Key::class, 'key', 'key');
    }
    public function infostatus()
    {
        return $this->belongsTo(Status::class, 'status', 'id');
    }
    

    public function pegawaiKepalaCabang()
    {
        return $this->hasOne(PegawaiKepalaCabang::class, 'id_user');
    }

    public function pegawaiSupervisor()
    {
        return $this->hasOne(PegawaiSupervisor::class, 'id_user');
    }

    public function pegawaiAdminKas()
    {
        return $this->hasOne(PegawaiAdminKas::class, 'id_user');
    }

    public function pegawaiAccountOfficer()
    {
        return $this->hasOne(PegawaiAccountOffice::class, 'id_user');
    }


    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'id_wilayah');
    }
    public function cabang()
    {
        return $this->belongsTo(Wilayah::class, 'id_cabang');
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'jabatan_id',
        'id_cabang',
        'id_wilayah',
        'key',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $dispatchesEvents = [
        'created' => UserRegisteredMobile::class,
    ];
}
