<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Route::get('/','App\Http\Controllers\TestController@Test');
// Route::get('/test2',function(){

// 	if(file_exists(public_path('DIR_AP040506_ALL.zip'))){
// 		File::delete(public_path('DIR_AP040506_ALL.zip'));
// 	}
// 	return view('test');
// });
// Route::post('/test3','App\Http\Controllers\TestController@Test2');