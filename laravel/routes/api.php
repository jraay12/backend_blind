<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactsController;
use App\Http\Middleware\Permission;
use App\Http\Middleware\UserPermission;



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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['adminPermission']], function(){
        Route::post("/create", [UserController::class, "create"]);
        Route::get("/total-user", [UserController::class, "userDetails"]);
     });
     Route::group(['middleware' => ['userPermission']], function(){
        Route::post("/add-contacts", [ContactsController::class, "contacts"]);
        Route::get("/contacts-details/{id}", [ContactsController::class, "contactDetails"]);
    });
});

 




Route::get('/unauthorized', function () {
    return response([
        'status' => 'error',
        'message' => 'Unauthorized'
    ], 404);
})->name('unauthorized');


Route::post("/login", [UserController:: class, "login"]);