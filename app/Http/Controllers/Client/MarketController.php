<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Menu;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\GD\Driver;

class MarketController extends Controller
{
    // public function AllMenu() {
    //     $id = Auth::guard('client')->id();
    //     $menu = Menu::where('client_id', $id)->orderBy('id', 'desc')->get();
    //     return view('client.backend.menu.all_menu', compact('menu'));
    // }


    public function AllMenu(){
        $menu = Menu::latest()->get();
        return view('client.backend.menu.all_menu', compact('menu'));
    } 
    //End Method

    public function AddMenu(){

        return view('client.backend.menu.add_menu');
    } 
    //End Method

    public function StoreMenu(Request $request) {
        if($request->file('image')){
            $image = $request->file('image');
            $manage = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'
                        .$image->getClientOriginalExtension();
            $img = $manage->read($image);
            $img->resize(300, 300)->save(public_path('upload/menu_images/'
                .$name_gen));
            $save_url = 'upload/menu_images/'.$name_gen;

            Menu::create([
                'menu_name' => $request->menu_name,
                'image' => $save_url,
            ]);

        }
        
        $notification = array(
            'message' => 'Create Menu Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.menu')->with($notification);
    }
    // End Method

    public function EditMenu($id) {
        $menu = Menu::find($id);
        return view('client.backend.menu.edit_menu', compact('menu'));

    }
    // End Method
    
    public function UpdateMenu(Request $request) {
        $cat_id = $request->id;

        if($request->file('image')){
            $image = $request->file('image');
            $manage = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'
                        .$image->getClientOriginalExtension();
            $img = $manage->read($image);
            $img->resize(300, 300)->save(public_path('upload/menu_images/'
                .$name_gen));
            $save_url = 'upload/menu_images/'.$name_gen;

            Menu::find($cat_id)->update([
                'menu_name' => $request->menu_name,
                'image' => $save_url,
            ]);

            $notification = array(
                'message' => 'Update Menu Successfully',
                'alert-type' => 'success'
            );
        } else {
            Menu::find($cat_id)->update([
                'menu_name' => $request->menu_name,
            ]);
            $notification = array(
                'message' => 'Update Menu Successfully',
                'alert-type' => 'success'
            );
        }
        

        return redirect()->route('all.menu')->with($notification);
    }
    // End Method

    public function DeleteMenu($id) {
        $item = Menu::find($id);
        $img = $item->image;
        unlink($img);

        Menu::find($id)->delete();
        
        $notification = array(
            'message' => 'Delete Menu Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }
    // End Method

}
