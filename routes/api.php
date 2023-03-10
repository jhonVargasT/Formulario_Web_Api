<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassportController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\FormularioController;



Route::post('register', [PassportController::class, 'register']);
Route::post('login', [PassportController::class, 'login']);

//pantalla de incio del proyecto
Route::get('portadaProyecto',[ProyectoController::class,'getProyecto']);
Route::get('formulario',[FormularioController::class,'getFormulario']);

// put all api protected routes here
Route::middleware('auth:api')->group(function () {
    Route::post('user-detail', [PassportController::class, 'userDetail']);
    Route::post('logout', [PassportController::class, 'logout']);
});
