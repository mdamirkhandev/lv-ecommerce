<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{

    public function index()
    {
        $categories = Category::orderBy('name', 'asc')
            ->with('subCategory')
            ->where('status', 1)
            ->get();
        $brands = Brand::orderBy('name', 'asc')
            ->where('status', 1)
            ->get();
        $products = Product::orderBy('id', 'desc')
            ->where('status', 1)
            ->get();
        return view('user.shop', compact('categories', 'brands', 'products'));
    }
}
