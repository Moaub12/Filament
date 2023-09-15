<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;
    protected $table = 'tags';
    protected $fillable = [
        'name',
        'image',
    ];
    public function products()
    {
        return $this->belongsToMany(related:Product::class,table:"tag_products");
    }
}
