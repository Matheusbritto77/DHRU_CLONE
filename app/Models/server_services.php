<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class server_services extends Model
{
    use HasFactory;

    protected $fillable = [
        'servicename',
        'serviceid',
        'cost',
        'referenceid',
        'user_id',
        'IMEI',
        'status',
        'code',
        'uuid',
        'Qnt',
    ];
}
