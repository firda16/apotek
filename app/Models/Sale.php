<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Supplier;
use Illuminate\Notifications\Notifiable;

class Sale extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'queue_number',
        'payment_method',
        'discount',
        'total_price',
        'status',   
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
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    // Sale.php
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
