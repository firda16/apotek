<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'queue_number',
        'payment_method',
        'discount',
        'total_price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
    // Sale.php
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
