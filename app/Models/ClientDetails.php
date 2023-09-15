<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Client;


class ClientDetails extends Model
{
    use HasFactory;
    protected $table='client_details';
    protected $fillable=[
        'fname',
        'lname',
        'company_name',
        'country',
        'city',
        'state',
        'zip',
        'client_id'


    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
