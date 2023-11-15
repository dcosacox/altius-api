<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConnectController;
use App\Http\Controllers\SitesController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/sites', [SitesController::class, 'index']);
Route::post('/download', [ConnectController::class, 'download']);
Route::post('/connect', [ConnectController::class, 'connect']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
