<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable= [
        'status',
        'ordered_date',
        'client_id',
        'payment_id',
        'coupon_id',
        'total',
        'total_discounted',
    ];
    public function products(){
        return $this->belongsToMany(Product::class,table:'order_lines');
    }   
    public function client(){
    return $this->belongsTo(Client::class);
    }
    public function orderDetails() 
    {
        return $this->hasOne(OrderDetails::class);
    }
    
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
    public function coupon(){
        return $this->belongsTo(Coupon::class);
    }
    
}

