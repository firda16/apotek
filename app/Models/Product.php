<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name','category_id','unit',
        'price','stock','description',
    ];

    public function purchase(){
        return $this->belongsTo(Purchase::class);
    }
    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function purchaseItems() {
        return $this->hasMany(PurchaseItem::class);
    }

}
