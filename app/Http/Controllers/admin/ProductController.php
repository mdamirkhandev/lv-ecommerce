<?php

namespace App\Http\Controllers\admin;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\TempImages;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::latest('id');
        if (!empty($request->get('keyword'))) {
            $products = $products->where('title', 'like', '%' . $request->get('keyword') . '%');
        }
        $products = $products->with('product_images')->paginate(8);

        return view('admin.products.list', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ];

        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            $product = new Product;
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->save();

            //Save gallery pics
            if (!empty($request->image_array)) {
                foreach ($request->image_array as $temp_image_id) {
                    $tempImageInfo = TempImages::find($temp_image_id);
                    $extArray = explode('.', $tempImageInfo->name);
                    $ext = last($extArray); // like jpg,gif, png etc
                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->image = 'NULL';
                    $productImage->save();
                    $imageName = $product->id . '-' . $productImage->id . '-' . time() . '.' . $ext;
                    $productImage->image = $imageName;
                    $productImage->save();

                    // Generate Product Thumbnails
                    // Large Image
                    $sPath = public_path() . '/temp/' . $tempImageInfo->name;
                    $dPath = public_path() . '/uploads/product/large/' . $imageName;
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($sPath);
                    $image->scale(height: 1400);
                    $image->toPng()->save($dPath);

                    // Small Image
                    $dPath = public_path() . '/uploads/product/small/' . $imageName;
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($sPath);
                    $image->cover(300, 300);
                    $image->toPng()->save($dPath);
                }
            }
            flash('Product Added successfully', 'success');

            return response()->json([
                'status' => true,
                'message' => 'Product Added successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id, Request $request)
    {
        $product = Product::find($id);

        if (empty($product)) {
            return redirect()->route('products.index')->with('error', 'Product Not found', 'error');
        }

        $categories = Category::orderBy('name')->get();

        $subCategories = SubCategory::where('category_id', $product->category_id)->get();

        $productImages = ProductImage::where('product_id', $product->id)->get();

        $brands = Brand::orderBy('name')->get();

        return view('admin.products.edit', compact('categories', 'subCategories', 'productImages', 'brands', 'product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products,slug,' . $product->id . ',id',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku,' . $product->id . ',id',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ];

        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->save();

            flash('Product Updated successfully', 'success');

            return response()->json([
                'status' => true,
                'message' => 'Product Updated successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (empty($product)) {
            flash('Product Not found', 'error');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Product Not found'
            ]);
        }
        $productImages = ProductImage::where('product_id', $id)->get();
        if (!empty($productImages)) {
            foreach ($productImages as $productImage) {
                File::delete(public_path('uploads/product/small/' . $productImage->image));
                File::delete(public_path('uploads/product/large/' . $productImage->image));
            }
            ProductImage::where('product_id', $id)->delete();
        }

        $product->delete();

        flash('Product Deleted successfully', 'success');

        return response()->json([
            'status' => true,
            'message' => 'Product Deleted Successfully'
        ]);
    }
}
