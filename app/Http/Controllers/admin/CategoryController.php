<?php

namespace App\Http\Controllers\admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TempImages;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::latest();

        if (!empty($request->get('keyword'))) {
            $categories = $categories->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        $categories = $categories->paginate(10);

        // return view('admin.category.list', ['categories' => $categories]);
        return view('admin.category.list', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories',
            'status' => 'required',
        ]);

        if ($validator->passes()) {
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();
            // Save Image Here
            if (!empty($request->image_id)) {
                $tempImage = TempImages::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);
                $newImageName = $category->id . '-' . time() . '.' . $ext;
                $sPath = public_path() . '/temp/' . $tempImage->name;
                $dPath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($sPath, $dPath);
                $nPath = public_path() . '/uploads/category/thumb/' . $newImageName;
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($dPath);
                // crop the best fitting 5:3 (600x360) ratio and resize to 600x360 pixel
                $image->cover(450, 600);
                $image->toPng()->save($nPath);

                $category->image = $newImageName;
                $category->save();
            }

            session()->flash('success', 'Category Added Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Category Added Successfully'
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id, Request $request)
    {
        //
        $category = Category::find($id);
        if ($category) {
            return view('admin.category.edit', compact('category'));
        } else {
            return redirect()->route('categories.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (empty($category)) {
            session()->flash('error', 'Category not found');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'errors' => 'Category not found'
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $category->id . ',id',
            'status' => 'required',
        ]);

        if ($validator->passes()) {
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();
            $oldImage = $category->image;
            // Save Image Here
            if (!empty($request->image_id)) {
                $tempImage = TempImages::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);
                $newImageName = $category->id . '-' . time() . '.' . $ext;
                $sPath = public_path() . '/temp/' . $tempImage->name;
                $dPath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($sPath, $dPath);

                $nPath = public_path() . '/uploads/category/thumb/' . $newImageName;
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($dPath);
                // crop the best fitting 5:3 (600x360) ratio and resize to 600x360 pixel
                $image->cover(450, 600);
                $image->toPng()->save($nPath);

                $category->image = $newImageName;
                $category->save();

                File::delete(public_path() . '/uploads/category/' . $oldImage);
            }

            session()->flash('success', 'Category Updated Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Category Updated Successfully'
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
        $category = Category::find($id);

        if (empty($category)) {
            session()->flash('error', 'Category Not found');
            return response()->json([
                'status' => true,
                'message' => 'Category Not found'
            ]);
        }

        File::delete(public_path() . '/uploads/category/' . $category->image);

        $category->delete();

        session()->flash('success', 'Category Deleted Successfully');

        return response()->json([
            'status' => true,
            'message' => 'Category Deleted Successfully'
        ]);
    }
}
