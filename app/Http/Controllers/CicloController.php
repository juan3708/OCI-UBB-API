<?php

namespace App\Http\Controllers;

use App\Models\Ciclo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CicloController extends Controller
{
    public function all()
    {
        $ciclo = DB::table('ciclo as c')->select('a.id', 'a.anio', 'a.nombre', 'a.presupuesto',
        DB::raw('DATE_FORMAT(c.fecha_inicio, "%d-%m-%Y") as fecha_inicio'), 
        DB::raw('DATE_FORMAT(c.fecha_termino, "%d-%m-%Y") as fecha_termino'),
        'c.coordinador_id')->get();

        $data = [
            'code' => 200,
            'ciclos' => $ciclo
        ];
        return response() ->json($data);
    }

    public function create(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'anio' => 'required',
                'nombre' => 'required',
                'fecha_inicio' => 'required|date_format:Y-m-d',
                'presupuesto' => 'required',
                'coordinador_id' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $ciclo = Ciclo::where('nombre', $request ->nombre)->first();
                if (empty($ciclo)) {
                    $ciclo = new Ciclo();
                    $ciclo -> anio = $request -> anio;
                    $ciclo -> nombre = $request -> nombre;
                    $ciclo -> fecha_inicio = $request -> fecha_inicio;
                    $ciclo -> fecha_termino = $request -> fecha_termino;
                    $ciclo -> presupuesto = $request -> presupuesto;
                    $ciclo -> coordinador_id =$request -> coordinador_id;
                    $ciclo ->save();
                    $data = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Se ha creado correctamente el ciclo',
                            'ciclo' => $ciclo
                        ];
                } else {
                    $data = [
                            'code' => 400,
                            'status' => 'error',
                            'message' => 'Ya existe un ciclo con ese nombre'
                        ];
                }
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al crear el ciclo'
            ];
        }
        return response() ->json($data);
    }

    public function edit(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'id' => 'required',
                'anio' => 'required',
                'nombre' => 'required',
                'fecha_inicio' => 'required|date_format:Y-m-d',
                'presupuesto' => 'required',
                'coordinador_id' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $ciclo = Ciclo::find($request->id);
                if (!empty($ciclo)) {
                    $ciclo -> anio = $request -> anio;
                    $ciclo -> nombre = $request -> nombre;
                    $ciclo -> fecha_inicio = $request -> fecha_inicio;
                    $ciclo -> fecha_termino = $request -> fecha_termino;
                    $ciclo -> presupuesto = $request -> presupuesto;
                    $ciclo -> coordinador_id = $request -> coordinador_id;
                    $ciclo ->save();
                    $data = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Se ha editado correctamente el ciclo',
                            'ciclo' => $ciclo
                        ];
                } else {
                    $data = [
                            'code' => 400,
                            'status' => 'error',
                            'message' => 'No existe un ciclo asociado'
                        ];
                }
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al editar el ciclo'
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
                'mensaje' => 'Debe ingresar un ID de un ciclo'
            ];
        } else {
            $ciclo = Ciclo::find($request->id);
            if (empty($ciclo)) {
                $data = [
                    'code' =>400,
                    'status' => 'error',
                    'mensaje' => 'No se encontro un ciclo asociada al ID'
                ];
            } else {
                $ciclo ->delete();
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
