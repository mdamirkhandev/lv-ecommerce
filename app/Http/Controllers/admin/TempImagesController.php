<?php

namespace App\Http\Controllers\admin;

use App\Models\TempImages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TempImagesController extends Controller
{
    public function create(Request $request)
    {
        if ($request->image) {
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $newName = time() . '.' . $ext;

            $tempImage = new TempImages();
            $tempImage->name =  $newName;
            $tempImage->save();

            $image->move(public_path() . '/temp', $newName);
            // Generate thumbnail
            $sPath = public_path() . '/temp/' . $newName;
            $dPath = public_path() . '/temp/thumb/' . $newName;
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($sPath);
            $image->cover(300, 275);
            $image->toPng()->save($dPath);

            return response()->json([
                'status' => true,
                'image_id' => $tempImage->id,
                'ImagePath' => asset('/temp/thumb/' . $newName),
                'message' => 'Image uploaded successfully'
            ]);
        }
    }
}
