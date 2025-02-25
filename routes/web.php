<?php

use App\Http\Controllers\ProductController;

use Illuminate\Support\Facades\Route;


Route::get('/', [ProductController::class, 'index']);
Route::post('/add-product', [ProductController::class, 'addProduct']);
Route::get('/add-product', [ProductController::class, 'getAddProduct']);
