<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\WardController;
use App\Http\Controllers\Client\MarketController;
use App\Http\Controllers\Client\GalleryController;
use App\Http\Controllers\Client\CouponController;
use App\Http\Controllers\Admin\ManageController;
use App\Http\Controllers\Frontend\HomeController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [UserController::class, 'Index'])->name('index');

Route::get('/dashboard', function () {
    return view('frontend.dashboard.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/profile/store', [UserController::class, 'ProfileStore'])->name('profile.store');
    Route::get('/user/logout', [UserController::class, 'UserLogout'])->name('user.logout');
    Route::get('/change/password', [UserController::class, 'ChangePassword'])->name('change.password');
    Route::post('/user/password/update', [UserController::class, 'UserPasswordUpdate'])->name('user.password.update');

    // Wishlist Data For User
    Route::get('/all/wishlist', [HomeController::class, 'AllWishlist'])->name('all.wishlist');
    Route::get('/remove/wishlist/{id}', [HomeController::class, 'RemoveWishlist'])->name('remove.wishlist');
});

require __DIR__.'/auth.php';

Route::middleware('admin')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'AdminDashboard'])->name('admin.dashboard');
    Route::get('/admin/profile', [AdminController::class, 'AdminProfile'])->name('admin.profile');
    Route::post('/admin/profile/store', [AdminController::class, 'AdminProfileStore'])->name('admin.profile.store');
    Route::get('/admin/change/password', [AdminController::class, 'AdminChangePassword'])->name('admin.change.password');
    Route::post('/admin/password/update', [AdminController::class, 'AdminPasswordUpdate'])->name('admin.password.update');
});

Route::get('/admin/login', [AdminController::class, 'AdminLogin'])->name('admin.login');
Route::post('/admin/login_submit', [AdminController::class, 'AdminLoginSubmit'])->name('admin.login_submit');
Route::get('/admin/logout', [AdminController::class, 'AdminLogout'])->name('admin.logout');
Route::get('/admin/forget_password', [AdminController::class, 'AdminForgetPassword'])->name('admin.forget_password');
Route::post('/admin/password_submit', [AdminController::class, 'AdminPasswordSubmit'])->name('admin.password_submit');
Route::get('/admin/reset-password/{token}/{email}', [AdminController::class, 'AdminResetPassword']);
Route::post('/admin/reset_password_submit', [AdminController::class, 'AdminResetPasswordSubmit'])->name('admin.reset_password_submit');

/// All route for client

Route::middleware('client')->group(function () {
    Route::get('/client/dashboard', [ClientController::class, 'ClientDashboard'])->name('client.dashboard');
    Route::get('/client/profile', [ClientController::class, 'ClientProfile'])->name('client.profile');
    Route::post('/client/profile/store', [ClientController::class, 'ClientProfileStore'])->name('client.profile.store');
    Route::get('/client/change/password', [ClientController::class, 'ClientChangePassword'])->name('client.change.password');
    Route::post('/client/password/update', [ClientController::class, 'ClientPasswordUpdate'])->name('client.password.update');
});

Route::get('/client/login', [ClientController::class, 'ClientLogin'])->name('client.login');
Route::get('/client/register', [ClientController::class, 'ClientRegister'])->name('client.register');
Route::post('/client/register/submit', [ClientController::class, 'ClientRegisterSubmit'])->name('client.register.submit');
Route::post('/client/login_submit', [ClientController::class, 'ClientLoginSubmit'])->name('client.login_submit');
Route::get('/client/logout', [ClientController::class, 'ClientLogout'])->name('client.logout');

/// End Admin Middleware 
Route::middleware('admin')->group(function () {

    // ALL ADMIN CATEGORY
    Route::controller(CategoryController::class)->group(function(){
        Route::get('/all/category', 'AllCategory')->name('all.category');
        
        Route::get('/add/category', 'AddCategory')->name('add.category');
        Route::post('/store/category', 'StoreCategory')->name('category.store');
        
        Route::get('/edit/category/{id}', 'EditCategory')->name('edit.category');
        Route::post('/update/category', 'UpdateCategory')->name('category.update');
        
        Route::get('/delete/category{id}', 'DeleteCategory')->name('delete.category');
    });

    // ALL ADMIN City
    Route::controller(CityController::class)->group(function(){
        Route::get('/all/city', 'AllCity')->name('all.city');
        
        Route::get('/add/city', 'AddCity')->name('add.city');
        Route::post('/store/city', 'StoreCity')->name('city.store');
        
        Route::get('/edit/city/{id}', 'EditCity');
        Route::post('/update/city', 'UpdateCity')->name('city.update');
        
        Route::get('/delete/city{id}', 'DeleteCity')->name('delete.city');
    });

    // ALL ADMIN District
    Route::controller(DistrictController::class)->group(function() {
        Route::get('/city/districts/{cityId}', 'AllDistricts')->name('all.districts');
        
        Route::get('/city/district/create/{cityId}', 'CreateDistrict')->name('add.district');
        Route::post('/city/district/store/{cityId}', 'StoreDistrict')->name('district.store');
        
        Route::get('/edit/district/{id}', 'EditDistrict');
        Route::post('/district/update', 'UpdateDistrict')->name('district.update');
        
        Route::get('/district/delete/{id}', 'DeleteDistrict')->name('delete.district');
    });

    // ALL ADMIN Ward
    Route::controller(WardController::class)->group(function() {
        Route::get('/district/wards/{districtId}', 'AllWards')->name('all.wards');
        
        Route::get('/district/ward/create/{districtId}', 'CreateWard')->name('add.ward');
        Route::post('/district/ward/store/{districtId}', 'StoreWard')->name('store.ward');
        
        Route::get('/edit/ward/{id}', 'EditWard');
        Route::post('/ward/update', 'UpdateWard')->name('ward.update');
        
        Route::get('/ward/delete/{id}', 'DeleteWard')->name('delete.ward');
    });
    
    // ALL ADMIN Product
    Route::controller(ManageController::class)->group(function(){
        Route::get('/admin/all/product', 'AdminAllProduct')->name('admin.all.product');
        
        Route::get('/admin/add/product', 'AdminAddProduct')->name('admin.add.product');
        Route::post('/admin/store/product', 'AdminStoreProduct')->name('admin.product.store');
        
        Route::get('/admin/edit/product/{id}', 'AdminEditProduct')->name('admin.edit.product');
        Route::post('/admin/update/product', 'AdminUpdateProduct')->name('admin.product.update');
        
        Route::get('/admin/delete/product/{id}', 'AdminDeleteProduct')->name('admin.delete.product');
    });
    
    // ALL ADMIN Product
    Route::controller(ManageController::class)->group(function(){
        Route::get('/pending/market', 'PendingMarket')->name('pending.market');
        Route::get('/clientChangeStatus', 'ClientChangeStatus');
        Route::get('/approve/market', 'ApproveMarket')->name('approve.market');
    });
    
    // ALL ADMIN Banner
    Route::controller(ManageController::class)->group(function(){
        Route::get('/all/banner', 'AllBanner')->name('all.banner');
        Route::post('/banner/store', 'BannerStore')->name('banner.store');
        Route::get('/edit/banner/{id}', 'EditBanner')->name('edit.banner');
        Route::post('/banner/update', 'BannerUpdate')->name('banner.update');
        Route::get('/banner/delete/{id}', 'DeleteBanner')->name('delete.banner');
    });

}); // End Admin Middleware


Route::middleware(['client', 'status'])->group(function () {
    Route::controller(MarketController::class)->group(function(){
        Route::get('/all/menu', 'AllMenu')->name('all.menu');
        
        Route::get('/add/menu', 'AddMenu')->name('add.menu');
        Route::post('/store/menu', 'StoreMenu')->name('menu.store');
        
        Route::get('/edit/menu/{id}', 'EditMenu')->name('edit.menu');
        Route::post('/update/menu', 'UpdateMenu')->name('menu.update');
        
        Route::get('/delete/menu/{id}', 'DeleteMenu')->name('delete.menu');
    });

    Route::controller(MarketController::class)->group(function(){
        Route::get('/all/product', 'AllProduct')->name('all.product');
        
        Route::get('/add/product', 'AddProduct')->name('add.product');
        Route::post('/store/product', 'StoreProduct')->name('product.store');
        
        Route::get('/edit/product/{id}', 'EditProduct')->name('edit.product');
        Route::post('/update/product', 'UpdateProduct')->name('product.update');
        
        Route::get('/delete/product/{id}', 'DeleteProduct')->name('delete.product');
    });

    
    Route::controller(GalleryController::class)->group(function(){
        Route::get('/all/gallery', 'AllGallery')->name('all.gallery');
        
        Route::get('/add/gallery', 'AddGallery')->name('add.gallery');
        Route::post('/store/gallery', 'StoreGallery')->name('gallery.store');
        
        Route::get('/edit/gallery/{id}', 'EditGallery')->name('edit.gallery');
        Route::post('/update/gallery', 'UpdateGallery')->name('gallery.update');
        
        Route::get('/delete/gallery/{id}', 'DeleteGallery')->name('delete.gallery');
        // Route::get('/changeStatus', 'ChangeStatus');
    });

    Route::controller(CouponController::class)->group(function(){
        Route::get('/all/coupon', 'AllCoupon')->name('all.coupon');
        
        Route::get('/add/coupon', 'AddCoupon')->name('add.coupon');
        Route::post('/store/coupon', 'StoreCoupon')->name('coupon.store');
        
        Route::get('/edit/coupon/{id}', 'EditCoupon')->name('edit.coupon');
        Route::post('/update/coupon', 'UpdateCoupon')->name('coupon.update');
        
        Route::get('/delete/coupon/{id}', 'DeleteCoupon')->name('delete.coupon');
        // Route::get('/changeStatus', 'ChangeStatus');
    });
}); // End Client Middleware

/// For All User
Route::get('/changeStatus', [MarketController::class, 'ChangeStatus']);

Route::controller(HomeController::class)->group(function(){
    Route::get('/market/details/{id}', 'MarketDetails')->name('market.details');
    Route::post('/add-wish-list/{id}', 'AddWishlist');

    // Route::post('/store/coupon', 'StoreCoupon')->name('coupon.store');
    
    // Route::get('/edit/coupon/{id}', 'EditCoupon')->name('edit.coupon');
    // Route::post('/update/coupon', 'UpdateCoupon')->name('coupon.update');
    
    // Route::get('/delete/coupon/{id}', 'DeleteCoupon')->name('delete.coupon');
    // Route::get('/changeStatus', 'ChangeStatus');
});
