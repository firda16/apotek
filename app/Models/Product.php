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

    // public function getAvailableStockAttribute()
    // {
    //     $validItems = $this->purchaseItems()
    //         ->whereDate('expiry_date', '>', Carbon::today())
    //         ->get();

    //     $totalPurchased = $validItems->sum('quantity');
    //     $totalSold = $validItems->sum('sold_quantity');

    //     return $totalPurchased - $totalSold;
    // }
    public function getAvailableStockAttribute()
    {
        return $this->purchaseItems
            ->filter(function ($item) {
                return is_null($item->expiry_date) || $item->expiry_date > now();
            })
            ->sum(fn($item) => $item->quantity - $item->sold_quantity);
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
    public function scopeOutOfStock($query)
    {
        return $query->with('purchaseItems')
            ->whereHas('purchaseItems', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('expiry_date', '>', now())
                        ->orWhereNull('expiry_date');
                });
            })
            ->whereDoesntHave('purchaseItems', function ($q) {
                $q->whereRaw('(quantity - sold_quantity) > 0')
                    ->where(function ($qq) {
                        $qq->where('expiry_date', '>', now())
                            ->orWhereNull('expiry_date');
                    });
            });
    }

    // public function scopeOutOfStock($query)
    // {
    //     return $query->with('purchaseItems')
    //         ->whereDoesntHave('purchaseItems', function ($q) {
    //             $q->whereRaw('(quantity - sold_quantity) > 0');
    //         });
    // }


}
