<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function(){
    return view('home');
});

Route::get('/home', function(){
    return view('home');
})->name('home');

Route::get('/about', function(){
    return view('about');
})->name('about');

Route::get('/contact', function(){
    return view('contact');
})->name('contact');

Route::get('/login', function(){
    return view('login');
})->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    // warehouse auth
    Route::get('/warehouses/{id}', [WarehouseController::class, 'show'])->name('warehouses.show');
    Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
    Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
    Route::delete('/warehouses/{id}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');

});

Route::middleware('auth')->group(function () {
    // Section Routes under a specific warehouse
    Route::post('/warehouses/{warehouseId}/sections', [SectionController::class, 'store'])->name('sections.store');
    Route::get('/warehouses/{warehouseId}/sections', [SectionController::class, 'index'])->name('sections.index');
    Route::delete('/warehouses/{warehouseId}/sections/{sectionId}', [SectionController::class, 'destroy'])->name('sections.destroy');
});

Route::middleware('auth')->group(function () {
    // Product routes
    Route::post('/warehouses/{warehouseId}/sections/{sectionId}/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/warehouses/{warehouseId}/sections/{sectionId}/products', [ProductController::class, 'index'])->name('products.index');
    Route::delete('/warehouses/{warehouseId}/sections/{sectionId}/products/{productId}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/warehouses/{warehouseId}/sections/{sectionId}/products/{productId}', [ProductController::class, 'show'])->name('products.show');
    Route::put('/warehouses/{warehouseId}/sections/{sectionId}/products/{productId}', [ProductController::class, 'update'])->name('products.update');
});


require __DIR__.'/auth.php';
