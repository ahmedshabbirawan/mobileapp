<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Post\AppPostController;
use App\Http\Controllers\Media\AppMediaController;

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
    
    Route::group(['prefix' => 'template', 'as' => 'template.'], function () {
        Route::get('/', [AppPostController::class, 'index'])->name('list');
        Route::post('/', [AppPostController::class, 'store'])->name('store');
        Route::patch('/{id}', [AppPostController::class, 'update'])->name('update');
       // Route::get('/status/{id}', [AppPostController::class, 'status'])->name('status');
       // Route::delete('/delete/{id}', [AppPostController::class, 'destroy'])->name('delete');
        Route::get('/{id}', [AppPostController::class, 'show'])->name('view'); 
    });

    Route::group(['prefix' => 'media', 'as' => 'api.media.'], function () {
        Route::get('/', [AppMediaController::class, 'index'])->name('list');
        Route::post('/store', [AppMediaController::class, 'store'])->name('store');
        Route::post('/update/{id}', [AppMediaController::class, 'update'])->name('update');
        Route::get('/status/{id}', [AppMediaController::class, 'status'])->name('status');
        Route::delete('/delete/{id}', [AppMediaController::class, 'destroy'])->name('delete');
        Route::get('/view/{id}', [AppMediaController::class, 'show'])->name('view'); 
    });

});




