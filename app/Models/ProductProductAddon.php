<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductProductAddon extends Model
{
    use HasFactory;
    protected $table = 'product_product_addons';
    protected $fillable = [
        'product_id',
        'product_addon_id',
    ];
}
