<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfessionalController;
use Illuminate\Support\Facades\Route;

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
Route::group(['prefix' => '/consultation'], function () {

    Route::middleware(['auth:sanctum', 'abilities:consultation:self-manage'])->group(function () {
        Route::post('/announce', [ProfessionalController::class, 'storeAnnounce']);
//        Route::put('/announce/{id}', [ProfessionalController::class, 'updateAnnounce']);

    });
});

Route::group(['prefix' => '/checkout', 'middleware' => 'auth:sanctum'], function () {
    Route::post('store', [CheckoutController::class, 'store']);

});
