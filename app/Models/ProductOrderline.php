<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrderline extends Model
{
    use HasFactory;
    protected $table = 'product_orderlines';
    protected $fillable = [
        'product_id',
        'order_line_id'
    ];
}
