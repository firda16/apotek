<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SaleItem extends Model
{
    use Notifiable;
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit',
        'unit_price',
        'discount',
        'total_price'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchaseItem()
{
    return $this->belongsTo(PurchaseItem::class, 'purchase_item_id');
}


}
