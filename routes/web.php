<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShareController;
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
    Route::put('/warehouses/{id}', [WarehouseController::class, 'update'])->name('warehouses.update'); 

});

Route::middleware('auth')->group(function () {
    // Section Routes under a specific warehouse
    Route::post('/warehouses/{warehouseId}/sections', [SectionController::class, 'store'])->name('sections.store');
    Route::get('/warehouses/{warehouseId}/sections', [SectionController::class, 'index'])->name('sections.index');
    Route::delete('/warehouses/{warehouseId}/sections/{sectionId}', [SectionController::class, 'destroy'])->name('sections.destroy');
    Route::put('/warehouses/{warehouseId}/sections/{sectionId}', [SectionController::class, 'update'])->name('sections.update'); 
    Route::put('/warehouses/{warehouseId}/sections/{sectionId}/position', [SectionController::class, 'updatePosition'])->name('sections.updatePosition');
});

Route::middleware('auth')->group(function () {
    // Product routes
    Route::post('/warehouses/{warehouseId}/sections/{sectionId}/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/warehouses/{warehouseId}/sections/{sectionId}/products', [ProductController::class, 'index'])->name('products.index');
    Route::delete('/warehouses/{warehouseId}/sections/{sectionId}/products/{productId}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/warehouses/{warehouseId}/sections/{sectionId}/products/{productId}', [ProductController::class, 'show'])->name('products.show');
    Route::put('/warehouses/{warehouseId}/sections/{sectionId}/products/{productId}', [ProductController::class, 'update'])->name('products.update');
});

Route::middleware('auth')->group(function () {
    // Share routes
    Route::post('/warehouses/{warehouseId}/share', [ShareController::class, 'share'])->name('warehouses.share');
    Route::get('/warehouses/{warehouseId}/shared-users', [ShareController::class, 'listShared'])->name('warehouses.sharedUsers');
    Route::delete('/warehouses/{warehouseId}/shared-users/{userId}', [ShareController::class, 'unshare'])->name('warehouses.unshare');
});

Route::middleware('auth')->group(function () {
    
    // API route to get current user data
    Route::get('/api/user', function () {
        return response()->json(auth()->user());
    });
});



require __DIR__.'/auth.php';
