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

        foreach ($images as $gimg) {
            $manage = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'
                        .$gimg->getClientOriginalExtension();
            $img = $manage->read($gimg);
            $img->resize(800, 800)->save(public_path('upload/gallery_images/'
                .$name_gen));
            $save_url = 'upload/gallery_images/'.$name_gen;

            Gallery::insert([
                'client_id' => Auth::guard('client')->id(),
                'gallery_img' => $save_url,
            ]);
        }
        
        $notification = array(
            'message' => 'Insert Gallery Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.gallery')->with($notification);
    }
    // End Method

    public function EditGallery($id) {
        $gallery = Gallery::find($id);
        return view('client.backend.gallery.edit_gallery', compact('gallery'));
    }
    // End Method
    
    public function UpdateGallery(Request $request) {
        
        $gallery_id = $request->id;

        if($request->file('gallery_img')){
            $image = $request->file('gallery_img');
            $manage = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'
                        .$image->getClientOriginalExtension();
            $img = $manage->read($image);
            $img->resize(800, 800)->save(public_path('upload/gallery_images/'
                .$name_gen));
            $save_url = 'upload/gallery_images/'.$name_gen;

            $gallery = Gallery::find($gallery_id);
            if ($gallery->gallery_img) {
                $img = $gallery->gallery_img;
                unlink($img);
            }

            $gallery->update([
                'gallery_img' => $save_url
            ]);

            $notification = array(
                'message' => 'Update Gallery Successfully',
                'alert-type' => 'success'
            );
    
            return redirect()->route('all.gallery')->with($notification);
        } else {
            $notification = array(
                'message' => 'No Image Selected for Update',
                'alert-type' => 'warning'
            );
    
            return redirect()->route('all.gallery')->with($notification);
        }
    }
    // End Method

    public function DeleteGallery($id) {
        $item = Gallery::find($id);
        $img = $item->gallery_img;
        unlink($img);

        Gallery::find($id)->delete();
        
        $notification = array(
            'message' => 'Delete Gallery Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }
    // End Method
}
