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
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ManageController;
use App\Http\Controllers\Admin\ManageOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Client\MarketController;
use App\Http\Controllers\Client\GalleryController;
use App\Http\Controllers\Client\CouponController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Frontend\FilterController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [UserController::class, 'Index'])->name('index');

Route::get('/dashboard', function () {
    return view('frontend.dashboard.profile');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/profile/store', [UserController::class, 'ProfileStore'])->name('profile.store');
    Route::get('/user/logout', [UserController::class, 'UserLogout'])->name('user.logout');
    Route::get('/change/password', [UserController::class, 'ChangePassword'])->name('change.password');
    Route::post('/user/password/update', [UserController::class, 'UserPasswordUpdate'])->name('user.password.update');

    // Wishlist Data For User
    Route::get('/all/wishlist', [HomeController::class, 'AllWishlist'])->name('all.wishlist');
    Route::get('/remove/wishlist/{id}', [HomeController::class, 'RemoveWishlist'])->name('remove.wishlist');

    // Order Data For User
    Route::controller(ManageOrderController::class)->group(function(){
        Route::get('/user/order/list', 'UserOrderList')->name('user.order.list');
        Route::get('/user/order/details/{id}', 'UserOrderDetails')->name('user.order.details'); 
        Route::get('/user/invoice/download/{id}', 'UserInvoiceDownload')->name('user.invoice.download'); 
    });
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
        Route::get('/all/category', 'AllCategory')->name('all.category')->middleware('permission:category.all');
        
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
    
    // ALL ADMIN Manage Order
    Route::controller(ManageOrderController::class)->group(function(){
        Route::get('/pending/order', 'PendingOrder')->name('pending.order');
        Route::get('/confirm/order', 'ConfirmOrder')->name('confirm.order');
        Route::get('/processing/order', 'ProcessingOrder')->name('processing.order');
        Route::get('/delivered/order', 'DeliveredOrder')->name('delivered.order');

        Route::get('/admin/order/details/{id}', 'AdminOrderDetails')->name('admin.order_details');
    });
    
    // ALL ADMIN REPORT 
    Route::controller(ReportController::class)->group(function(){
        Route::get('/admin/all/reports', 'AdminAllReports')->name('admin.all.reports');
        Route::post('/admin/search/bydate', 'AdminSearchBydate')->name('admin.search.bydate');
        Route::post('/admin/search/bymonth', 'AdminSearchBymonth')->name('admin.search.bymonth');
        Route::post('/admin/search/byyear', 'AdminSearchByyear')->name('admin.search.byyear');
    });

    // ALL ADMIN REVIEW 
    Route::controller(ReviewController::class)->group(function(){
        Route::get('/admin/pending/review', 'AdminPendingReview')->name('admin.pending.review');
        Route::get('/admin/approve/review', 'AdminApproveReview')->name('admin.approve.review'); 
        Route::get('/reviewchangeStatus', 'ReviewChangeStatus'); 
    });
    
    // ALL ADMIN PERMISSION 
    Route::controller(RoleController::class)->group(function(){
        Route::get('/all/permission', 'AllPermission')->name('all.permission');
        Route::get('/add/permission', 'AddPermission')->name('add.permission');
        Route::post('/store/permission', 'StorePermission')->name('permission.store');
        Route::get('/edit/permission/{id}', 'EditPermission')->name('edit.permission');
        Route::post('/update/permission', 'UpdatePermission')->name('permission.update');
        Route::get('/delete/permission/{id}', 'DeletePermission')->name('delete.permission');

        Route::get('/import/permission', 'ImportPermission')->name('import.permission');
        Route::get('/export', 'Export')->name('export');
        Route::post('/import', 'Import')->name('import');
    });
    
    // ALL ADMIN ROLES 
    Route::controller(RoleController::class)->group(function(){
        Route::get('/all/roles', 'AllRoles')->name('all.roles');
        Route::get('/add/roles', 'AddRoles')->name('add.roles');
        Route::post('/store/roles', 'StoreRoles')->name('roles.store');
        Route::get('/edit/roles/{id}', 'EditRoles')->name('edit.roles');
        Route::post('/update/roles', 'UpdateRoles')->name('roles.update');
        Route::get('/delete/roles/{id}', 'DeleteRoles')->name('delete.roles');
    });

    // ALL ADMIN ROLES IN PERMISSION 
    Route::controller(RoleController::class)->group(function(){
        Route::get('/add/roles/permission', 'AddRolesPermission')->name('add.roles.permission');
        Route::post('/role/permission/store', 'RolePermissionStore')->name('role.permission.store');
        Route::get('/all/roles/permission', 'AllRolesPermission')->name('all.roles.permission');

        Route::get('/admin/edit/roles/{id}', 'AdminEditRoles')->name('admin.edit.roles');
        Route::post('/admin/roles/update/{id}', 'AdminRolesUpdate')->name('admin.roles.update');

        Route::get('/admin/delete/roles/{id}', 'AdminDeleteRoles')->name('admin.delete.roles');
    });

    // MULTI ADMIN
    Route::controller(RoleController::class)->group(function(){
        Route::get('/all/admin', 'AllAdmin')->name('all.admin'); 
        Route::get('/add/admin', 'AddAdmin')->name('add.admin');
        Route::post('/admin/store', 'AdminStore')->name('admin.store');

        Route::get('/edit/admin/{id}', 'Editadmin')->name('edit.admin');
        Route::post('/admin/update/{id}', 'AdminUpdate')->name('admin.update');

        Route::get('/delete/admin/{id}', 'DeleteAdmin')->name('delete.admin');
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
    });
    
    Route::controller(ManageOrderController::class)->group(function(){
        Route::get('/all/clients/orders', 'AllClientsOrders')->name('all.clients.orders');
        Route::get('/client/order/details/{id}', 'ClientOrderDetails')->name('client.order_details');
    });
    
    // ALL CLIENT REPORT 
    Route::controller(ReportController::class)->group(function(){
        Route::get('/client/all/reports', 'ClientAllReports')->name('client.all.reports');
        Route::post('/client/search/bydate', 'ClientSearchBydate')->name('client.search.bydate');
        Route::post('/client/search/bymonth', 'ClientSearchBymonth')->name('client.search.bymonth');
        Route::post('/client/search/byyear', 'ClientSearchByyear')->name('client.search.byyear');
    });

    // ALL CLIENT REVIEW 
    Route::controller(ReviewController::class)->group(function(){
        Route::get('/client/all/reviews', 'ClientAllReviews')->name('client.all.reviews'); 
        
    });

}); // End Client Middleware

/// For All User
Route::get('/changeStatus', [MarketController::class, 'ChangeStatus']);

Route::controller(HomeController::class)->group(function(){
    Route::get('/market/details/{id}', 'MarketDetails')->name('market.details');
    Route::post('/add-wish-list/{id}', 'AddWishlist');

});

Route::controller(CartController::class)->group(function(){
    Route::get('/add_to_cart/{id}', 'AddToCart')->name('add_to_cart');
    Route::post('/cart/updateQuantity', 'UpdateCartQuantity')->name('cart.updateQuantity');
    Route::post('/cart/remove', 'CartRemove')->name('cart.remove');
    Route::post('/apply-coupon', 'ApplyCoupon');
    Route::get('/remove-coupon', 'RemoveCoupon');

    Route::get('/checkout', 'MarketCheckout')->name('checkout');

});


Route::controller(OrderController::class)->group(function(){
    Route::post('/cash_order', 'CashOrder')->name('cash_order');

    Route::post('/mark-notification-as-read/{notification}', 'MarkAsRead');

});

Route::controller(ManageOrderController::class)->group(function(){
    Route::get('/pening_to_confirm/{id}', 'PeningToConfirm')->name('pening_to_confirm');
    Route::get('/confirm_to_processing/{id}', 'ConfirmToProcessing')->name('confirm_to_processing');
    Route::get('/processing_to_delivered/{id}', 'ProcessingToDiliverd')->name('processing_to_delivered');
});

Route::controller(ReviewController::class)->group(function(){
    Route::post('/store/review', 'StoreReview')->name('store.review');  
    
});

Route::controller(FilterController::class)->group(function(){
    Route::get('/list/market', 'ListMarket')->name('list.market');  
    Route::get('/filter/products', 'FilterProducts')->name('filter.products');
    
});
