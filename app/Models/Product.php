<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'category_id',
        'unit',
        'price',
        // 'stock',
        'description',
    ];

    public function getAvailableStockAttribute()
    {
        $validItems = $this->purchaseItems()
            ->whereDate('expiry_date', '>', Carbon::today())
            ->get();

        $totalPurchased = $validItems->sum('quantity');
        $totalSold = $validItems->sum('sold_quantity');

        return $totalPurchased - $totalSold;
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
