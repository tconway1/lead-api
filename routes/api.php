<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/leads', [ApiController::class, 'index']);

Route::post('/leads', [ApiController::class, 'create']);

Route::patch('/leads/{id}', [ApiController::class, 'update']);

Route::delete('/leads/{id}', [ApiController::class, 'delete']);
