<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/registerForm','register');
Route::view('/login','login');
Route::view('/dashboard','dashboard');
Route::view('/products','products');
Route::view('/orders','orders');
Route::view('/reports','reports');
Route::view('/warehouses','warehouse');

Route::view('/add-product', 'add-product');