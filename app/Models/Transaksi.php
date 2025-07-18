<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi'; // pastikan sesuai nama tabel
    protected $fillable = [
        'kode_transaksi',
        'total_harga',
        'tanggal_transaksi', // atau bisa pakai created_at
        // tambah field lain jika ada
    ];

}
