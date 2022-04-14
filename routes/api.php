<?php

use App\Http\Controllers\TelegramController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/setCommands',[TelegramController::class,'setMyCommands'])->middleware(['auth']);
Route::get('/deleteCommands',[TelegramController::class,'deleteMyCommands'])->middleware(['auth']);
Route::get('/getUpdates',[TelegramController::class,'getUpdates'])->name('getUpdates');
Route::post('/handleUpdate',[TelegramController::class,'handleUpdate']);

Route::get('/setWebHook',[TelegramController::class,'setWebHook'])->middleware(['auth']);
Route::get('/deleteWebHook',[TelegramController::class,'deleteWebHook'])->middleware(['auth']);
Route::get('/getWebhookInfo',[TelegramController::class,'getWebhookInfo']);

Route::get('/sendDistribution',[TelegramController::class,'Distribution']);
