<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductImageController extends Controller
{
    public function update(Request $request)
{
    \Log::info($request->all()); // Debugging: Log request data

    $productId = $request->input('id');

    if (!$productId) {
        return response()->json([
            'status' => false,
            'message' => 'Product ID is required'
        ], 400);
    }

    $image = $request->file('image'); // Get the uploaded image

    if (!$image) {
        return response()->json([
            'status' => false,
            'message' => 'No image provided'
        ], 400);
    }

    $ext = $image->getClientOriginalExtension();
    $productImage = ProductImage::where('products_id', $productId)->first();

    if (!$productImage) {
        $productImage = new ProductImage();
        $productImage->products_id = $productId;
        $productImage->image = '';
        $productImage->save();
    }

    // Generate image name
    $imageName = $productId . '-' . $productImage->id . '-' . time() . '.' . $ext;
    $productImage->image = $imageName;
    $productImage->save();

    // Move the original image
    $largeDestinationPath = public_path('uploads/products/large/');
    $smallDestinationPath = public_path('uploads/products/small/');
    $image->move($largeDestinationPath, $imageName);

    // Create resized small image
    $largeImagePath = $largeDestinationPath . $imageName;
    list($width, $height) = getimagesize($largeImagePath);
    $smallImage = imagecreatefromstring(file_get_contents($largeImagePath));

    $newWidth = 300;
    $newHeight = 300;
    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($resizedImage, $smallImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Save resized image
    if ($ext == 'jpg' || $ext == 'jpeg') {
        imagejpeg($resizedImage, $smallDestinationPath . $imageName, 90);
    } elseif ($ext == 'png') {
        imagepng($resizedImage, $smallDestinationPath . $imageName);
    } elseif ($ext == 'gif') {
        imagegif($resizedImage, $smallDestinationPath . $imageName);
    }

    imagedestroy($smallImage);
    imagedestroy($resizedImage);

    return response()->json([
        'status' => true,
        'image_id' => $productImage->id,
        'ImagePath' => asset('uploads/products/large/' . $productImage->image),
        'message' => 'Image updated successfully'
    ]);
}
public function destroy(Request $request)
{
    // Find the image record
    $productImage = ProductImage::find($request->id);

    if (!$productImage) {
        return response()->json([
            'status' => false,
            'message' => 'Image not found'
        ], 404);
    }

    // Define image paths
    $largeImagePath = public_path('uploads/products/large/' . $productImage->image);
    $smallImagePath = public_path('uploads/products/small/' . $productImage->image);

    // Check if files exist before deleting
    if (File::exists($largeImagePath)) {
        File::delete($largeImagePath);
    }
    if (File::exists($smallImagePath)) {
        File::delete($smallImagePath);
    }

    // Delete database record
    $productImage->delete();

    return response()->json([
        'status' => true,
        'message' => 'Image deleted successfully'
    ]);
}


}
