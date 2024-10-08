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
        'show_price',
        'price'
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
