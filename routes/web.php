<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');




Route::post("/store", [UserController::class, 'store'])->name('store');
Route::post("/store/table/update", [UserController::class, 'update'])->name('update');
Route::post("/store/table/delete", [UserController::class, 'delete'])->name('delete');
Route::get('/store/table', [UserController::class, 'table'])->name('table');
