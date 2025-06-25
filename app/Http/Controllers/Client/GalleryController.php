<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\GD\Driver;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Carbon\Carbon;
use Illuminate\Support\Str;

use App\Models\Gallery;
use Cloudinary\Api\Upload\UploadApi; 

class GalleryController extends Controller
{
    public function AllGallery(){
        $client_id = Auth::guard('client')->id();
        $gallery = Gallery::where('client_id', $client_id)->latest()->get();
        return view('client.backend.gallery.all_gallery', compact('gallery'));
    } 
    //End Method

    public function AddGallery(){
        return view('client.backend.gallery.add_gallery');
    } 
    //End Method

    public function StoreGallery(Request $request) {
        $images = $request->file('gallery_img');
        $uploadApi = new UploadApi(); // ✅ Dùng SDK gốc

        foreach ($images as $gimg) {
            $uploaded = $uploadApi->upload($gimg->getRealPath(), [
                'folder' => 'gallery_images'
            ]);
            $secureUrl = $uploaded['secure_url']; // ← dùng array, không phải getSecurePath()

            Gallery::create([
                'client_id' => Auth::guard('client')->id(),
                'gallery_img' => $secureUrl,
            ]);
        }

        return redirect()->route('all.gallery')->with([
            'message' => 'Insert Gallery Successfully',
            'alert-type' => 'success'
        ]);
    }
    // End Method

    public function EditGallery($id) {
        $gallery = Gallery::find($id);
        return view('client.backend.gallery.edit_gallery', compact('gallery'));
    }
    // End Method
    
    public function UpdateGallery(Request $request) {
        $gallery_id = $request->id;
        $gallery = Gallery::find($gallery_id);
        $uploadApi = new UploadApi();

        if ($request->file('gallery_img')) {
            $image = $request->file('gallery_img');

            $uploaded = $uploadApi->upload($image->getRealPath(), [
                'folder' => 'gallery_images'
            ]);
            $secureUrl = $uploaded['secure_url'];

            // Xoá ảnh cũ nếu cần
            if ($gallery->gallery_img && str_contains($gallery->gallery_img, 'res.cloudinary.com')) {
                $publicId = basename(parse_url($gallery->gallery_img, PHP_URL_PATH));
                $publicId = 'gallery_images/' . pathinfo($publicId, PATHINFO_FILENAME);
                $uploadApi->destroy($publicId); // ✅ dùng SDK trực tiếp
            }

            $gallery->update([
                'gallery_img' => $secureUrl
            ]);

            return redirect()->route('all.gallery')->with([
                'message' => 'Update Gallery Successfully',
                'alert-type' => 'success'
            ]);
        }

        return redirect()->route('all.gallery')->with([
            'message' => 'No Image Selected for Update',
            'alert-type' => 'warning'
        ]);
    }

    // End Method

    public function DeleteGallery($id) {
        $item = Gallery::find($id);
        $uploadApi = new UploadApi();

        if ($item && $item->gallery_img && str_contains($item->gallery_img, 'res.cloudinary.com')) {
            $publicId = basename(parse_url($item->gallery_img, PHP_URL_PATH));
            $publicId = 'gallery_images/' . pathinfo($publicId, PATHINFO_FILENAME);
            $uploadApi->destroy($publicId); // ✅ xoá trực tiếp
        }

        $item->delete();

        return redirect()->back()->with([
            'message' => 'Delete Gallery Successfully',
            'alert-type' => 'success'
        ]);
    }

    // End Method
}
