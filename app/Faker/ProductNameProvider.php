<?php

namespace App\Faker;

use Faker\Provider\Base;

class ProductNameProvider extends Base
{
    protected static $productNames = [
        'iPhone', 'Galaxy S21', 'MacBook Pro', 'Dell XPS', 'AirPods',
        'Sony WH-1000XM4', 'Nike Air Max', 'Adidas Ultraboost',
        'Samsung QLED TV', 'Canon EOS R5', 'GoPro HERO9', 'Fitbit Charge 4'
    ];

    public function productName()
    {
        return static::randomElement(static::$productNames);
    }
}
