<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;
    protected $table = 'produits';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'description',
        'quantity',
        'priceSale',
        'priceFav',
        'priceMax',
        'photo',
        'category',
        'status',
        'provider_id',
        'instagrammer_id'];   

        public function users()
        {
            return $this->hasMany(User::class);
        }

        public function images()
        {
            return $this->hasMany(Image::class);
        }

}

