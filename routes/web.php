<?php

use App\Http\Controllers\{DistributionsController, LoginController, SettingsController, UsersController};
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () { return redirect('dashboard'); });

Route::get('/dashboard', function () { return view('dashboard'); })->middleware(['auth'])->name('dashboard');

Route::get('/distributions', [DistributionsController::class, 'create'])->middleware(['auth'])->name('distributions');
Route::get("/createDistribution", [DistributionsController::class, 'createDistribution'])->middleware(['auth'])->name('createDistribution');
Route::post("/createDistribution", [DistributionsController::class, 'createDistributionPost'])->middleware(['auth'])->name('createDistributionPost');
Route::get("/deleteDistribution/{id}", [DistributionsController::class, 'delete'])->middleware(['auth'])->name('deleteDistribution');

Route::get('/users', [UsersController::class, 'create'])->middleware(['auth'])->name('users');
Route::get("/users/{userId}", [UsersController::class, 'createInfo'])->middleware(['auth'])->name('userInfo');
Route::post('/banUser', [UsersController::class, 'banUser'])->middleware(['auth'])->name('switchBan');

//TODO: Сделать настройки для смены токена и подключения вебхука
Route::get('/settings', [SettingsController::class, 'create'])->middleware(['auth'])->name('settings');
Route::post('/editSettings', [SettingsController::class, 'create'])->middleware(['auth']);

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware(['auth'])->name('logout');

