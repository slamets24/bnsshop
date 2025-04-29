<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FrontendController;

// Frontend Routes
Route::get('/', [FrontendController::class, 'welcome'])->name('welcome');
Route::get('/products', [FrontendController::class, 'products'])->name('products');
Route::get('/products/{product}', [FrontendController::class, 'productDetail'])->name('products.show');
Route::get('/categories', [FrontendController::class, 'categories'])->name('categories');
Route::get('/categories/{category}', [FrontendController::class, 'categoryProducts'])->name('categories.show');

// Dashboard Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('transactions', TransactionController::class)->except(['show']);
        Route::resource('shipments', ShipmentController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);
        Route::delete('/products/{product}/images/{image}', [ProductController::class, 'deleteImage'])->name('products.images.delete');
        Route::put('/products/{product}/links', [ProductController::class, 'updateLinks'])->name('products.links.update');
        Route::post('/products/{product}/images', [ProductController::class, 'uploadImages'])->name('products.images.upload');
        Route::post('/products/{product}/toggle-active', [ProductController::class, 'toggleActive'])->name('products.toggle-active');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
