<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;
    protected $table='order_details';
    protected $fillable=[
    'fname',
    'lname',
    'company_name',
    'country_region',
    'adress',
    'town_city',
    'state',
    'zip_code',
    'phone',
    'email',
    'notes',
    'order_id',
];
    public function order() 
    {
        return $this->belongsTo(Order::class);
    }
}
