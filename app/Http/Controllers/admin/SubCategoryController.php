<?php

namespace App\Http\Controllers\admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $subCategories = SubCategory::select('sub_categories.*', 'categories.name as categoryName')
            ->latest('sub_categories.id')
            ->leftJoin('categories', 'categories.id', 'sub_categories.category_id');

        if (!empty($request->get('keyword'))) {
            $subCategories = $subCategories->where('sub_categories.name', 'like', '%' . $request->get('keyword') . '%');
            $subCategories = $subCategories->orWhere('categories.name', 'like', '%' . $request->get('keyword') . '%');
        }

        $subCategories = $subCategories->paginate(10);

        return view('admin.sub_category.list', compact('subCategories'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $categories = Category::get();
        return view('admin.sub_category.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category' => 'required',
            'status' => 'required'
        ]);
        if ($validator->passes()) {
            $subCategory = new SubCategory();
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->showMenu = $request->showMenu;
            $subCategory->category_id = $request->category;
            $subCategory->save();

            flash('Sub-Category Added successfully', 'success');

            return response()->json([
                'status' => true,
                'message' => 'Sub-Category Added Successfully'
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
    public function edit(string $id, Request $request)
    {
        $subCategory = SubCategory::find($id);
        if (empty($subCategory)) {
            flash('Sub-Category not Found !!', 'error');
            return redirect()->route('sub-category.index');
        }
        $categories = Category::get();
        return view('admin.sub_category.edit', compact('categories', 'subCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $subCategory = SubCategory::find($id);

        if (empty($subCategory)) {
            flash('Sub-Category not Found !!', 'error');
            return response()->json([
                'status' => false,
                'notFound' => true
            ]);

            // return redirect()->route('sub-category.index');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories,slug,' . $subCategory->id . ',id',
            'category' => 'required',
            'status' => 'required'
        ]);
        if ($validator->passes()) {
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->showMenu = $request->showMenu;
            $subCategory->category_id = $request->category;
            $subCategory->save();

            flash('Sub-Category Updated successfully', 'success');

            return response()->json([
                'status' => true,
                'message' => 'Sub-Category Updated Successfully'
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
        $subCategory = Subcategory::find($id);

        if (empty($subCategory)) {
            flash('Sub-Category Not found', 'error');
            return response()->json([
                'status' => true,
                'message' => 'Sub-Category Not found'
            ]);
        }

        $subCategory->delete();

        flash('Sub-Category Deleted successfully', 'success');

        return response()->json([
            'status' => true,
            'message' => 'Sub-Category Deleted Successfully'
        ]);
    }
}
