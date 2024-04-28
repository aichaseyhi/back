<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
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
        'taille',
        'color',
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

        public function colors()
        {
            return $this->belongsToMany(Color::class);
        }
        public function sizes()
        {
            return $this->belongsToMany(Size::class);
        }

        public function subcategories()
        {
            return $this->belongsToMany(SubCategory::class);
        }
}

