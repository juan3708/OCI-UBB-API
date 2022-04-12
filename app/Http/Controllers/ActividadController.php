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
        $actividad = DB::table('actividad')->select('*')->get();
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
                'fecha' => 'required',
                'nombre' => 'required',
                'descripcion' => 'required',
                'ciclo_id' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Ingrese todos los datos porfavor',
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
                'code' => 400,
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
                'fecha' => 'required',
                'nombre' => 'required',
                'descripcion' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Ingrese todos los datos porfavor',
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
                            'code' => 400,
                            'status' => 'error',
                            'message' => 'No existe una actividades asociado'
                        ];
                }
            }
        } else {
            $data = [
                'code' => 400,
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
                'mensaje' => 'Debe ingresar un ID de una actividad'
            ];
        } else {
            $actividad = Actividad::find($request->id);
            if (empty($actividad)) {
                $data = [
                    'code' =>400,
                    'status' => 'error',
                    'mensaje' => 'No se encontro una actividad asociada al ID'
                ];
            } else {
                $actividad ->delete();
                $data = [
                    'code' =>200,
                    'status' => 'success',
                    'mensaje' => 'Se ha eliminado correctamente'
                ];
            }
        }
        return response() -> json($data);
    }
}
