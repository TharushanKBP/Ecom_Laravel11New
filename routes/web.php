<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthAdmin;
use App\Http\Controllers\WishlistController;


Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [ShopController::class, 'product_details'])->name('shop.product.details');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add_to_cart'])->name('cart.add');
Route::put('cart/increase_quantity/{rowId}', [CartController::class, 'increase_cart_quantit'])->name('cart.qty.increase');
Route::put('cart/decrease_quantity/{rowId}', [CartController::class, 'decrease_cart_quantit'])->name('cart.qty.decrease');
Route::delete('cart/remove/{rowId}', [CartController::class, 'remove_item'])->name('cart.item.remove');
Route::delete('/cart/empty', [CartController::class, 'empty_cart'])->name('cart.empty');

Route::post('/cart/apply_coupon', [CartController::class, 'apply_coupon_code'])->name('cart.coupon.apply');

Route::post('wishlist/add', [WishlistController::class, 'add_to_wishlist'])->name('wishlist.add');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::delete('wishlist/item/remove/{rowId}', [WishlistController::class, 'remove_from_wishlist'])->name('wishlist.item.remove');
Route::delete('/wishlist/clear', [WishlistController::class, 'empty_wishlist'])->name('wishlist.item.clear');
Route::post('wishlist/move_to_cart/{rowId}', [WishlistController::class, 'move_to_cart'])->name('wishlist.move_to_cart');

Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');


Route::middleware(['auth'])->group(function () {
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
});

// Admin routes (for authenticated admins only)
Route::middleware(['auth', AuthAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');

        // Brand management routes
        Route::get('/brands', [AdminController::class, 'brands'])->name('brands');
        Route::get('/brand/add', [AdminController::class, 'add_brand'])->name('brand.add');
        Route::post('/brand/store', [AdminController::class, 'brand_store'])->name('brand.store');
        Route::get('/brand/edit/{id}', [AdminController::class, 'brand_edit'])->name('brand.edit');
        Route::put('/brand/update/{id}', [AdminController::class, 'brand_update'])->name('brand.update');
        Route::delete('/admin/brands/{id}', [AdminController::class, 'brand_destroy'])->name('brand.delete');

        // Category management routes
        Route::get('/admin/categories', [AdminController::class, 'categories'])->name('categories');
        Route::get('/categories/add', [AdminController::class, 'add_categories'])->name('categories.add');
        Route::post('/categories/store', [AdminController::class, 'categories_store'])->name('categories.store');
        Route::get('/categories/edit/{id}', [AdminController::class, 'categories_edit'])->name('categories.edit');
        Route::put('/categories/update/{id}', [AdminController::class, 'categories_update'])->name('categories.update');
        Route::delete('/admin/categories/{id}', [AdminController::class, 'categories_destroy'])->name('categories.delete');

        // Product management routes
        Route::get('/admin/products', [AdminController::class, 'products'])->name('products');
        Route::get('/products/add', [AdminController::class, 'add_products'])->name('products.add');
        Route::post('/products/store', [AdminController::class, 'products_store'])->name('products.store');
        Route::get('/products/edit/{id}', [AdminController::class, 'products_edit'])->name('products.edit');
        Route::put('/products/update/{id}', [AdminController::class, 'products_update'])->name('products.update');
        Route::delete('/admin/products/{id}', [AdminController::class, 'products_destroy'])->name('products.delete');

        // Coupon management routes 
        Route::get('/admin/coupons', [AdminController::class, 'coupons'])->name('coupons');
        Route::get('/admin/coupons/add', [AdminController::class, 'coupons_add'])->name('coupons.add');
        Route::post('/admin/coupons/store', [AdminController::class, 'coupons_store'])->name('coupons.store');
        Route::get('/admin/coupons/edit/{id}', [AdminController::class, 'coupons_edit'])->name('coupons.edit');
        Route::put('/admin/coupons/update', [AdminController::class, 'coupons_update'])->name('coupons.update');
        Route::delete('/admin/coupons/{id}/delete', [AdminController::class, 'coupons_delete'])->name('coupons.delete');
    });
