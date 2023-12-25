<?php

namespace App\Http\Controllers\admin;

use App\Models\TempImages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TempImagesController extends Controller
{
    public function create(Request $request)
    {
        $image = $request->image;

        if (!empty($image)) {
            $ext = $image->getClientOriginalExtension();
            $newName = time() . '.' . $ext;

            $tempImage = new TempImages();
            $tempImage->name =  $newName;
            $tempImage->save();

            $image->move(public_path() . '/temp', $newName);

            return response()->json([
                'status' => true,
                'image_id' => $tempImage->id,
                'message' => 'Image upload successfully'
            ]);
        }
    }
}