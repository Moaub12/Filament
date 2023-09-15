<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Client;

class Adress extends Model
{
    use HasFactory;
    
    protected $table="adresses";
    protected $fillable=[
        'name',
        'client_id'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
