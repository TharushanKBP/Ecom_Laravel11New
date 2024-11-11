<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthAdmin;

// Authentication routes
Auth::routes();

// Public route for home page
Route::get('/', [HomeController::class, 'index'])->name('home.index');

// User dashboard route (for authenticated users)
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
        // Route::get('/products/add', [AdminController::class, 'add_products'])->name('products.add');
        // Route::post('/products/store', [AdminController::class, 'products_store'])->name('products.store');
        // Route::get('/products/edit/{id}', [AdminController::class, 'products_edit'])->name('products.edit');
        // Route::put('/products/update/{id}', [AdminController::class, 'products_update'])->name('products.update');
        // Route::delete('/admin/products/{id}', [AdminController::class, 'products_destroy'])->name('products.delete');
    });
