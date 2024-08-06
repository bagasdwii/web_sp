<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
    protected $primaryKey = 'key';

    use HasFactory;
    public function users()
    {
        return $this->hasMany(Key::class, 'key', 'key');
    }
}
