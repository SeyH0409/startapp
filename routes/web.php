<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Auth::routes();

Route::group(['middleware' => 'auth'], function(){
  Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
  Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
  Route::get('/create', [App\Http\Controllers\HomeController::class, 'create'])->name('create');
  Route::post('/store', [App\Http\Controllers\HomeController::class, 'store'])->name('post');
  Route::get('/edit/{id}', [App\Http\Controllers\HomeController::class, 'edit'])->name('edit');
  Route::post('/update/{id}', [App\Http\Controllers\HomeController::class, 'update'])->name('update');
  Route::post('/delete/{id}', [App\Http\Controllers\HomeController::class, 'delete'])->name('delete');
});
