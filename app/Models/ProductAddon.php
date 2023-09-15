<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAddon extends Model
{
    use HasFactory;
    protected $table = 'product_addons';
    protected $fillable = [
        'order_line_id',
    ];
    // public function product()
    // {
    //     return $this->belongsTo(Product::class);
    // }
    public function order_line()
    {
        return $this->belongsTo(OrderLine::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class,table:'product_product_addons');
    }
}
