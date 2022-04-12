<?php

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

//Rutas Coordinador
Route::get('/coordinador/all',[CoordinadorController::class,'all']);
Route::post('/coordinador/create',[CoordinadorController::class,'create']);
Route::put('/coordinador/edit',[CoordinadorController::class,'edit']);
Route::delete('/coordinador/delete',[CoordinadorController::class,'delete']);

//Rutas Ciclo
Route::get('/ciclo/all',[CicloController::class,'all']);
Route::post('/ciclo/create',[CicloController::class,'create']);
Route::put('/ciclo/edit',[CicloController::class,'edit']);
Route::delete('/ciclo/delete',[CicloController::class,'delete']);

//Rutas Actividad  
Route::get('/actividad/all',[ActividadController::class,'all']);
Route::post('/actividad/create',[ActividadController::class,'create']);
Route::put('/actividad/edit',[ActividadController::class,'edit']);
Route::delete('/actividad/delete',[ActividadController::class,'delete']);

//Rutas Competencia  
Route::get('/competencia/all',[CompetenciaController::class,'all']);
Route::post('/competencia/create',[CompetenciaController::class,'create']);
Route::put('/competencia/edit',[CompetenciaController::class,'edit']);
Route::delete('/competencia/delete',[CompetenciaController::class,'delete']);

//Rutas Gastos  
Route::get('/gastos/all',[GastosController::class,'all']);
Route::post('/gastos/create',[GastosController::class,'create']);
Route::put('/gastos/edit',[GastosController::class,'edit']);
Route::delete('/gastos/delete',[GastosController::class,'delete']);

//Rutas Detalle  
Route::get('/detalle/all',[DetalleController::class,'all']);
Route::post('/detalle/create',[DetalleController::class,'create']);
Route::put('/detalle/edit',[DetalleController::class,'edit']);
Route::delete('/detalle/delete',[DetalleController::class,'delete']);

//Rutas Noticia  
Route::get('/noticia/all',[NoticiaController::class,'all']);
Route::post('/noticia/create',[NoticiaController::class,'create']);
Route::put('/noticia/edit',[NoticiaController::class,'edit']);
Route::delete('/noticia/delete',[NoticiaController::class,'delete']);

//Rutas Rol  
Route::get('/rol/all',[RolController::class,'all']);
Route::post('/rol/create',[RolController::class,'create']);
Route::put('/rol/edit',[RolController::class,'edit']);
Route::delete('/rol/delete',[RolController::class,'delete']);

//Rutas Establecimiento  
Route::get('/establecimiento/all',[EstablecimientoController::class,'all']);
Route::post('/establecimiento/create',[EstablecimientoController::class,'create']);
Route::put('/establecimiento/edit',[EstablecimientoController::class,'edit']);
Route::delete('/establecimiento/delete',[EstablecimientoController::class,'delete']);

//Rutas Alumno  
Route::get('/alumno/all',[AlumnoController::class,'all']);
Route::post('/alumno/create',[AlumnoController::class,'create']);
Route::put('/alumno/edit',[AlumnoController::class,'edit']);
Route::delete('/alumno/delete',[AlumnoController::class,'delete']);

//Rutas Clase  
Route::get('/clase/all',[ClaseController::class,'all']);
Route::post('/clase/create',[ClaseController::class,'create']);
Route::put('/clase/edit',[ClaseController::class,'edit']);
Route::delete('/clase/delete',[ClaseController::class,'delete']);

//Rutas Ayudante  
Route::get('/ayudante/all',[AyudanteController::class,'all']);
Route::post('/ayudante/create',[AyudanteController::class,'create']);
Route::put('/ayudante/edit',[AyudanteController::class,'edit']);
Route::delete('/ayudante/delete',[AyudanteController::class,'delete']);

//Rutas Profesor  
Route::get('/profesor/all',[ProfesorController::class,'all']);
Route::post('/profesor/create',[ProfesorController::class,'create']);
Route::put('/profesor/edit',[ProfesorController::class,'edit']);
Route::delete('/profesor/delete',[ProfesorController::class,'delete']);