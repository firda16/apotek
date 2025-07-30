<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi mass-assignment
    protected $fillable = [
        'nama',
        'telepon',
        'email',
        'address',
    ];

    // Jika kamu ingin menonaktifkan timestamps
    // public $timestamps = false;

    // Relasi: Customer memiliki banyak Sales
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
