<?php

use App\Models\Category;
use App\Models\ProductImage;

function getCategories()
{
    return Category::orderBy('name', 'ASC')
        ->with('sub_category')
        ->where('Status', 1)
        ->where('ShowMenu', 'Yes')
        ->get();
}


function getProductImage($productID)
{

    return ProductImage::where('product_id', $productID)->first();
}
