<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WalletController;

Route::post('register',[AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group( function () {

    Route::post('logout', [AuthController::class,'logout']);

    Route::get('user/details',[AuthController::class,'userDetails']);

    Route::post('add/wallet',[WalletController::class,'addWalletMoney']);

    Route::post('buy/cookies',[WalletController::class,'buyCookies']);
});