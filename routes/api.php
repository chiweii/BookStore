<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\Api\Book\BookLikeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware(['auth:sanctum','scope:user-info'])->get('/user', function (Request $request) {
Route::middleware(['auth:api','scope:user-info'])->get('/user', function (Request $request) {	
    return $request->user(); 
});

Route::apiResource('types',TypeController::class);
Route::apiResource('books',BookController::class);
Route::apiResource('books.likes', BookLikeController::class)->only([
	'index','store'
]);

// STATUS 說明

/*
201 請求已經被實現，而且有一個新的資源已經依據請求的需要而建立
409 請求存在衝突無法處理該請求，也就是新增失敗(該筆資料可能已存在)

*/