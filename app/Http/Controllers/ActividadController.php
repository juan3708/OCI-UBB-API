<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ActividadController extends Controller
{
    public function all()
    {
       /* $actividad = DB::table('actividad as a')->select('a.nombre','a.fecha','a.descripcion','a.id',
        DB::raw('DATE_FORMAT(a.fecha, "%d-%m-%Y") as fecha'), 'a.ciclo_id')->get();*/

        $actividad = Actividad::with('ciclo','gastos')->get();

        $data = [
            'code' => 200,
            'actividades' => $actividad
        ];
        return response() ->json($data);
    }

    public function create(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'fecha' => 'required|date_format:Y-m-d',
                'nombre' => 'required',
                'descripcion' => 'required',
                'ciclo_id' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $actividad = new Actividad();
                $actividad -> fecha = $request -> fecha;
                $actividad -> nombre = $request -> nombre;
                $actividad -> descripcion = $request -> descripcion;
                $actividad -> ciclo_id = $request -> ciclo_id;
                $actividad ->save();
                $data = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Se ha creado correctamente la actividad',
                            'actividad' => $actividad
                        ];
            }
        } else {
            $data = [
                'code' => 401,
                'status' => 'error',
                'message' => 'Error al crear la actividad'
            ];
        }
        return response() ->json($data);
    }

    public function edit(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'fecha' => 'required|date_format:Y-m-d',
                'nombre' => 'required',
                'descripcion' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $actividad = Actividad::find($request->id);
                if (!empty($actividad)) {
                    $actividad -> fecha = $request -> fecha;
                    $actividad -> nombre = $request -> nombre;
                    $actividad -> descripcion = $request -> descripcion;
                    $actividad ->save();
                    $data = [
                                'code' => 200,
                                'status' => 'success',
                                'message' => 'Se ha editado correctamente la actividad',
                                'actividad' => $actividad
                            ];
                } else {
                    $data = [
                            'code' => 401,
                            'status' => 'error',
                            'message' => 'No existe una actividades asociado'
                        ];
                }
            }
        } else {
            $data = [
                'code' => 401,
                'status' => 'error',
                'message' => 'Error al editar la actividad'
            ];
        }
        return response() ->json($data);
    }

    public function delete(Request $request)
    {
        if ($request->id == '') {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Debe ingresar un ID de una actividad'
            ];
        } else {
            $actividad = Actividad::find($request->id);
            if (empty($actividad)) {
                $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro una actividad asociada al ID'
                ];
            } else {
                $actividad ->delete();
                $data = [
                    'code' =>200,
                    'status' => 'success',
                    'message' => 'Se ha eliminado correctamente'
                ];
            }
        }
        return response() -> json($data);
    }
    public function getById(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'id' =>'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $actividad = Actividad::find($request ->id);
                if (empty($actividad)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro la actividad asociado al id'
                ];
                } else {
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'actividad' => $actividad
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al buscar la actividad'
            ];
        }
        return response() -> json($data);
    }
}
