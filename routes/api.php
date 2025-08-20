<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\MpesaC2BController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/mpesa/callback', [MpesaController::class, 'callback']);
Route::post('/mpesa/c2b/validate', [MpesaC2BController::class, 'validateC2B'])
    ->name('mpesa.c2b.validate');
Route::post('/mpesa/c2b/confirm', [MpesaC2BController::class, 'confirmC2B'])
    ->name('mpesa.c2b.confirm');
