<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nip extends Model
{
    use HasFactory;
    protected $fillable = [
        'nip',
       
      

    ];
    public function users()
    {
        return $this->hasMany(User::class, 'nip', 'nip');
    }
}
