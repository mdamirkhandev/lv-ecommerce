<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ShopController extends Controller
{

    public function index(Request $request, $catSlug = null, $subCatSlug = null)
    {
        $catSelected = '';
        $subCatSelected = '';
        $brandsArray = [];

        $categories = Category::orderBy('name', 'asc')
            ->with('sub_category')
            ->where('status', 1)
            ->get();
        $brands = Brand::orderBy('name', 'asc')
            ->where('status', 1)
            ->get();

        $products = Product::where('status', 1);

        if (!empty($catSlug)) {
            $category = Category::where('slug', $catSlug)->first();
            $products = $products->where('category_id', $category->id);
            $catSelected = $category->id;
        }
        if (!empty($subCatSlug)) {
            $subCategory = SubCategory::where('slug', $subCatSlug)->first();
            $products = $products->where('sub_category_id', $subCategory->id);
            $subCatSelected = $subCategory->id;
        }

        if (!empty($request->get('brand'))) {
            $brandsArray = explode(',', $request->get('brand'));
            $products = $products->whereIn('brand_id', $brandsArray);
        }
        if ($request->get('min_price') != '' && $request->get('max_price') != '') {
            if ($request->get('max_price') >= 1500) {
                $products = $products->whereBetween('price', [intval($request->get('min_price')), 1000000]);
            } else {
                $products = $products->whereBetween('price', [intval($request->get('min_price')), intval($request->get('max_price'))]);
            }
        }
        $products = $products->orderBy('id', 'desc');
        $products = $products->paginate(9);

        $maxPrice = (intval($request->get('max_price')) == 0) ? 500 : intval($request->get('max_price'));
        $minPrice = intval($request->get('min_price'));

        return view('user.shop', compact('categories', 'brands', 'products', 'catSelected', 'subCatSelected', 'brandsArray', 'maxPrice', 'minPrice'));
    }

    public function product($slug)
    {
        $product = Product::where('slug', $slug)
            ->with('product_images')
            ->first();
        if (empty($product)) {
            abort(404);
        }
        return view('user.product', compact('product'));
    }
}
