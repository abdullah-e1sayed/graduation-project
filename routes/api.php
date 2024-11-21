<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AccessTokensController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\HelpMessageController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\VulnerabilityController;
use App\Http\Controllers\Api\IndicatorController;
use App\Http\Controllers\Api\ExcelImportController;

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

    // auth routes
Route::post('auth/register', [AccessTokensController::class , 'register'])
    ->middleware('guest:sanctum')->name('register');

Route::post('admin/auth/create-admin', [AccessTokensController::class , 'createAdmin'])
    ->middleware('auth:sanctum');

Route::post('auth/access-tokens', [AccessTokensController::class , 'store'])
    ->middleware('guest:sanctum');

Route::delete('auth/access-tokens/revoke',[AccessTokensController::class , 'destroy'])
    ->middleware('auth:sanctum');

Route::post('admin/auth/access-tokens', [AccessTokensController::class , 'store'])
    ->middleware('guest:sanctum');

Route::delete('admin/auth/access-tokens/revoke',[AccessTokensController::class , 'destroy'])
    ->middleware('auth:sanctum');

     // Profile and list users routes
Route::get('admin/list-users/{name?}/{email?}/{phone_number?}',[ProfileController::class , 'users'])
    ->middleware('auth:sanctum');
Route::get('profile',[ProfileController::class , 'Profile'])
    ->middleware('auth:sanctum');
Route::put('profile/edit',[ProfileController::class , 'update'])
    ->middleware('auth:sanctum');
Route::delete('profile/delete',[ProfileController::class , 'destroy'])
    ->middleware('auth:sanctum');
Route::put('profile/change-password',[ProfileController::class , 'changePassword'])
    ->middleware('auth:sanctum');


    //help messages routes
Route::get('admin/help-messages/{message?}',[HelpMessageController::class , 'index'])
    ->middleware('auth:sanctum');
Route::post('help-messages/send',[HelpMessageController::class , 'store'])
    ->middleware('auth:sanctum');
Route::post('admin/help-messages/answer/{id}',[HelpMessageController::class , 'update'])
    ->middleware('auth:sanctum');
Route::delete('admin/help-messages/delete-message/{id}',[HelpMessageController::class , 'destroy'])
    ->middleware('auth:sanctum');

    // notes routes
Route::apiResource('note', NoteController::class)->middleware('auth:sanctum');
    // vulnerability routes
Route::apiResource('vulnerability', VulnerabilityController::class)->middleware('auth:sanctum');
    // Indicator routes
Route::apiResource('indicator', IndicatorController::class)->middleware('auth:sanctum');
 
Route::post('/vulnerability/import', [ExcelImportController::class, 'import'])->name('import')->middleware('auth:sanctum');



