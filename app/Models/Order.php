<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $fillable = [
        'firstName', 
        'secondName',
        'email',
        'phone',
        'city',
        'post_code',
        'cardNumber',
        'securityCode',
        'CVV',
        'quantity',
        'totalPrice',
        'status'
    ];   
}

