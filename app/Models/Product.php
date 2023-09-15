<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table='products';
    protected $fillable=[
        'name',
        'description',
        'price',
        'image',
        'quantity',
        'feature',
        'category_id',
    ];
    public function tags()
    {
        return $this->belongsToMany(Tag::class,table:"tag_products");
    }
    public function category(){
        return $this->belongsTo(Category::class);
       }
    public function orders(){
        return $this->belongsToMany(Order::class,table:"order_lines");
    }   
    public function ingredientMainProducts()
    {
        return $this->belongsToMany(Product::class, 'ingredients', 'ingredient_id', 'product_id');
    }

    public function ingredients()
    {
        return $this->belongsToMany(Product::class, 'ingredients', 'product_id', 'ingredient_id')->withPivot('removable');
    }

    public function addonMainProducts()
    {
        return $this->belongsToMany(Product::class, 'addons', 'addon_id', 'product_id');
    }

    public function addons()
    {
        return $this->belongsToMany(Product::class, 'addons', 'product_id', 'addon_id');
    }

    public function removables()
    {
        return $this->belongsToMany(Product::class, 'ingredients', 'product_id', 'ingredient_id')
            ->wherePivot('removable', 1);
    }

    public function productAddons()
    {
        return $this->belongsToMany(ProductAddon::class,table:'product_product_addons');
    }
    public function productRemoves()
    {
        return $this->belongsToMany(ProductRemoves::class,table:'product_product_removes');
    }
    
    public function orderLine()
{
    return $this->belongsToMany(OrderLine::class,table:'product_orderlines');
}

    public function scopeOrderByOrderedCounterDescending($query)
    {
        return $query->orderBy('ordered_counter', 'desc');
    }
    
    
}
