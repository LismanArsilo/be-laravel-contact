<?php

use App\Http\Controllers\Api\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(ContactController::class)->group(function () {
    Route::get('/contact', 'getAllContact');
    Route::get('/contact/{id}', 'getOneContact');
    Route::post('/contact', 'createContact');
    Route::delete('/contact/{id}', 'deleteContact');
});
