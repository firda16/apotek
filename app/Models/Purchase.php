<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'category_id',
        'supplier_id',
        'cost_price',
        'quantity',
        'expiry_date',
        'payment_method',
        'image'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
