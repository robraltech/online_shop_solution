<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TempImage;


class TempImageController extends Controller
{

    public function create(Request $request)
    {
        $image = $request->file('image');

        if ($image) {
            $ext = $image->getClientOriginalExtension();
            $newName = time() . '.' . $ext;

            // Save temporary image record in the database
            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            // Move uploaded image to the temp directory
            $image->move(public_path('temp'), $newName);

            $sourcePath = public_path('temp/' . $newName);
            $destinationPath = public_path('temp/thumb/' . $newName);

            // Create thumbnail manually using GD library
            $this->resizeImage($sourcePath, $destinationPath, 300, 275);

            return response()->json([
                'status' => true,
                'image_id' => $tempImage->id,
                'ImagePath' => asset('temp/thumb/' . $newName),
                'message' => 'Image uploaded successfully'
            ]);
        }

        return response()->json(['status' => false, 'message' => 'No image uploaded']);
    }

    /**
     * Resize image without Intervention/Image (GD Library)
     */
    private function resizeImage($source, $destination, $width, $height)
    {
        list($origWidth, $origHeight, $imageType) = getimagesize($source);

        // Create image resource based on type
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($source);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($source);
                break;
            default:
                return false;
        }

        // Create blank canvas for new image
        $thumb = imagecreatetruecolor($width, $height);

        // Preserve transparency for PNG and GIF
        if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
            imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
        }

        // Resize and save the image
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($thumb, $destination, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumb, $destination, 9);
                break;
            case IMAGETYPE_GIF:
                imagegif($thumb, $destination);
                break;
        }

        imagedestroy($image);
        imagedestroy($thumb);

        return true;
    }
}
