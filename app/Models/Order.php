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
        'TVA',
        'shippingCost',
        'payment',
        'totalPrice',
        'status',
        'product_id'
    ];  
    
    public function produit()
    {
        return $this->belongsTo(Product::class);
    }
}

