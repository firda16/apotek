<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiItem extends Model
{
    protected $table = 'transaksi_items'; // sesuaikan nama tabel sebenarnya
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }
}
