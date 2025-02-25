<?php

use App\Http\Controllers\ProductController;

use Illuminate\Support\Facades\Route;


Route::get('/', [ProductController::class, 'index'])->name('getAddProduct');
Route::post('/add-product', [ProductController::class, 'createOrUpdateProduct'])->name('addProduct');
Route::get('/add-product', [ProductController::class, 'addProductPage']);
Route::get('get-all-products', [ProductController::class, 'getAllProducts'])->name('products.fetch');
Route::post('delete-product/{id}', [ProductController::class, 'deleteProduct'])->name('products.delete');
