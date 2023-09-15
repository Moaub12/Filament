<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRemove extends Model
{
    use HasFactory;
    protected $table = 'product_removes';
    protected $fillable = [
        'order_line_id',
    ];

    public function orderline()
    {
        return $this->belongsTo(OrderLine::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class,table:'product_product_removes');
    }
}
