<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Post\AppPostController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::group(['prefix' => 'post', 'as' => 'api.post.'], function () {
    Route::group(['prefix' => 'logo', 'as' => 'logo.'], function () {
        Route::get('/list', [AppPostController::class, 'index'])->name('list');
        Route::post('/store', [AppPostController::class, 'store'])->name('store');
        Route::post('/update/{id}', [AppPostController::class, 'update'])->name('update');
        Route::get('/status/{id}', [AppPostController::class, 'status'])->name('status');
        Route::delete('/delete/{id}', [AppPostController::class, 'destroy'])->name('delete');
        Route::get('/view/{id}', [AppPostController::class, 'show'])->name('view'); 
    });
});