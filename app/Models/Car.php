<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'db_classification',
        'chasiss_number',
        'car_manufacturer',
        'model',
        'year',
        'color',
        'fuel_type',
        'number',
        'content',
        'status',
        'status_camera',
        'show_price',
        'price',
        'buyer',
        'buying_date',
        'company_source',
        'korean_price',
        'price_in_dollar',
        'shipping_price',
        'custom_price',
        'fixing_price',
        'total_cost',
        'city',
        'arrival_date',
    ];

    public function carImages()
    {
        return $this->hasMany(CarImage::class);
    }

    public function carManufacturer()
    {
        return $this->belongsTo(CarMake::class, 'car_manufacturer');
    }

    public function carModel()
    {
        return $this->belongsTo(CarModel::class, 'model');
    }
}
