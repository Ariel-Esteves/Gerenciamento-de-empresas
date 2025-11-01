<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'updated_at',
        'created_at'

    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function categoryName()
    {
        return $this->category ? $this->category->name : 'Uncategorized';
    }
    public function brandName()
    {
        return $this->brand ? $this->brand->name : 'No Brand';
    }
    public function formattedPrice()
    {
        return '$' . number_format($this->price, 2);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }
}
