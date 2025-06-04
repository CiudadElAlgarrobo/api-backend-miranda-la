<?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserAccessController;
 
Route::group([
    //'middleware' => 'auth:api',
    //'middleware' => ['auth:api','permission:publish articles'],
    //'middleware' => ['auth:api'],
    'prefix' => 'auth',
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
});


Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'auth',
], function ($router) {
    Route::resource('roles', RolePermissionController::class);
    Route::get('permissions', [RolePermissionController::class, 'getAllPermissions']);
    Route::resource('users', UserAccessController::class);
});