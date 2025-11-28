<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi'; // pastikan sesuai nama tabel

    protected $fillable = [
    'invoice',
    'customer_name',
    'customer_phone',
    'payment_method',
    'total',
    'discount',
    'created_at'
];

public function items()
{
    return $this->hasMany(TransaksiItem::class);
}

    public function transaksiItems()
    {
        return $this->hasMany(TransaksiItem::class, 'transaksi_id'); // sesuaikan foreign key kalau beda
    }
    public function customer()
{
    return $this->belongsTo(Customer::class);
}

}
