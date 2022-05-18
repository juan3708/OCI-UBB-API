<?php

use App\Http\Controllers\ActividadController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AyudanteController;
use App\Http\Controllers\CicloController;
use App\Http\Controllers\ClaseController;
use App\Http\Controllers\CompetenciaController;
use App\Http\Controllers\CoordinadorController;
use App\Http\Controllers\DetallesController;
use App\Http\Controllers\EstablecimientoController;
use App\Http\Controllers\GastosController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\NivelController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\RolController;
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
Route::get('/coordinador/all', [CoordinadorController::class,'all']);
Route::post('/coordinador/create', [CoordinadorController::class,'create']);
Route::put('/coordinador/edit', [CoordinadorController::class,'edit']);
Route::post('/coordinador/delete', [CoordinadorController::class,'delete']);
Route::post('/coordinador/getbyid', [CoordinadorController::class,'getById']);


//Rutas Ciclo
Route::get('/ciclo/all', [CicloController::class,'all']);
Route::post('/ciclo/create', [CicloController::class,'create']);
Route::put('/ciclo/edit', [CicloController::class,'edit']);
Route::post('/ciclo/delete', [CicloController::class,'delete']);
Route::post('/ciclo/getbyid', [CicloController::class,'getById']);
Route::post('/ciclo/getbyfinishdate', [CicloController::class,'getCyclePerFinishDate']);
Route::post('/ciclo/chargeestablishments', [CicloController::class,'CycleHasEstablishments']);
Route::post('/ciclo/updateestablishments', [CicloController::class,'UpdateEstablishments']);
Route::post('/ciclo/deleteestablishments', [CicloController::class,'deleteEstablishmentPerCycle']);
Route::post('/ciclo/chargestudents', [CicloController::class,'CycleHasStudents']);
Route::post('/ciclo/updatecandidates', [CicloController::class,'UpdateCandidates']);
Route::post('/ciclo/updateenrolled', [CicloController::class,'Updateenrolled']);
Route::post('/ciclo/deletestudents', [CicloController::class,'deleteStudentsPerCycle']);
Route::post('/ciclo/getstudentscandidate', [CicloController::class,'getStudentsCandidatePerCycle']);
Route::post('/ciclo/getstudentscandidatepercycleperfinishdate', [CicloController::class,'getStudentsCandidatePerCyclePerFinishDate']);
Route::post('/ciclo/getstudentsenrolled', [CicloController::class,'getStudentsEnrolledPerCycle']);
Route::post('/ciclo/getstudentsenrolledpercycleperfinishdate', [CicloController::class,'getStudentsEnrolledPerCyclePerFinishDate']);


//Rutas Actividad
Route::get('/actividad/all', [ActividadController::class,'all']);
Route::post('/actividad/create', [ActividadController::class,'create']);
Route::put('/actividad/edit', [ActividadController::class,'edit']);
Route::post('/actividad/delete', [ActividadController::class,'delete']);
Route::post('/actividad/getbyid', [ActividadController::class,'getById']);

//Rutas Competencia
Route::get('/competencia/all', [CompetenciaController::class,'all']);
Route::post('/competencia/create', [CompetenciaController::class,'create']);
Route::put('/competencia/edit', [CompetenciaController::class,'edit']);
Route::post('/competencia/delete', [CompetenciaController::class,'delete']);
Route::post('/competencia/getbyid', [CompetenciaController::class,'getById']);
Route::post('/competencia/attach', [CompetenciaController::class,'competitionHasStudent']);
Route::post('/competencia/detach', [CompetenciaController::class,'deleteStudentsPerCompetition']);
Route::post('/competencia/updatePivot', [CompetenciaController::class,'editScorePerStudent']);

//Rutas Gastos
Route::get('/gastos/all', [GastosController::class,'all']);
Route::post('/gastos/create', [GastosController::class,'create']);
Route::put('/gastos/edit', [GastosController::class,'edit']);
Route::post('/gastos/delete', [GastosController::class,'delete']);
Route::post('/gastos/getbyid', [GastosController::class,'getById']);

//Rutas Detalle
Route::get('/detalle/all', [DetallesController::class,'all']);
Route::post('/detalle/create', [DetallesController::class,'create']);
Route::put('/detalle/edit', [DetallesController::class,'edit']);
Route::post('/detalle/delete', [DetallesController::class,'delete']);
Route::post('/detalle/getbyid', [DetallesController::class,'getById']);

//Rutas Noticia
Route::get('/noticia/all', [NoticiaController::class,'all']);
Route::post('/noticia/create', [NoticiaController::class,'create']);
Route::put('/noticia/edit', [NoticiaController::class,'edit']);
Route::post('/noticia/delete', [NoticiaController::class,'delete']);
Route::post('/noticia/getbyid', [NoticiaController::class,'getById']);

//Rutas Rol
Route::get('/rol/all', [RolController::class,'all']);
Route::post('/rol/create', [RolController::class,'create']);
Route::put('/rol/edit', [RolController::class,'edit']);
Route::post('/rol/delete', [RolController::class,'delete']);
Route::post('/rol/getbyid', [RolController::class,'getById']);

//Rutas Establecimiento
Route::get('/establecimiento/all', [EstablecimientoController::class,'all']);
Route::post('/establecimiento/create', [EstablecimientoController::class,'create']);
Route::put('/establecimiento/edit', [EstablecimientoController::class,'edit']);
Route::post('/establecimiento/delete', [EstablecimientoController::class,'delete']);
Route::post('/establecimiento/getbyid', [EstablecimientoController::class,'getById']);
Route::post('/establecimiento/chargestudents', [EstablecimientoController::class,'chargeStudentPerForm']);

//Rutas Alumno
Route::get('/alumno/all', [AlumnoController::class,'all']);
Route::post('/alumno/create', [AlumnoController::class,'create']);
Route::put('/alumno/edit', [AlumnoController::class,'edit']);
Route::post('/alumno/delete', [AlumnoController::class,'delete']);
Route::post('/alumno/getbyid', [AlumnoController::class,'getById']);

//Rutas Clase
Route::get('/clase/all', [ClaseController::class,'all']);
Route::post('/clase/create', [ClaseController::class,'create']);
Route::put('/clase/edit', [ClaseController::class,'edit']);
Route::post('/clase/delete', [ClaseController::class,'delete']);
Route::post('/clase/getbyid', [ClaseController::class,'getById']);
Route::post('/clase/chargestudents', [ClaseController::class,'LessonHasStudents']);
Route::post('/clase/updatelistlesson', [ClaseController::class,'UpdateListLesson']);
Route::post('/clase/deletestudents', [ClaseController::class,'deleteStudentPerLesson']);
Route::post('/clase/chargeteachers', [ClaseController::class,'LessonHasTeachers']);
Route::post('/clase/deleteteachers', [ClaseController::class,'deleteTeachersPerLesson']);
Route::post('/clase/chargeassistans', [ClaseController::class,'LessonHasAssistants']);
Route::post('/clase/deleteassistans', [ClaseController::class,'deleteAssistansPerLesson']);

//Rutas Ayudante
Route::get('/ayudante/all', [AyudanteController::class,'all']);
Route::post('/ayudante/create', [AyudanteController::class,'create']);
Route::put('/ayudante/edit', [AyudanteController::class,'edit']);
Route::post('/ayudante/delete', [AyudanteController::class,'delete']);
Route::post('/ayudante/getbyid', [AyudanteController::class,'getById']);

//Rutas Profesor
Route::get('/profesor/all', [ProfesorController::class,'all']);
Route::post('/profesor/create', [ProfesorController::class,'create']);
Route::put('/profesor/edit', [ProfesorController::class,'edit']);
Route::post('/profesor/delete', [ProfesorController::class,'delete']);
Route::post('/profesor/getbyid', [ProfesorController::class,'getById']);

//Rutas Nivel
Route::get('/nivel/all', [NivelController::class,'all']);
Route::post('/nivel/create', [NivelController::class,'create']);
Route::put('/nivel/edit', [NivelController::class,'edit']);
Route::post('/nivel/delete', [NivelController::class,'delete']);
Route::post('/nivel/getbyid', [NivelController::class,'getById']);
Route::post('/nivel/levelassociate', [NivelController::class,'alumnoHasLevel']);

// Rutas Email

Route::post('/mail/inv', [MailController::class,'invitations']);

