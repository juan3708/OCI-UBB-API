<?php

namespace App\Http\Controllers;

use App\Models\Clase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClaseController extends Controller
{
    public function all()
    {
        $clase = DB::table('clase')->select('*')->get();
        $data = [
            'code' => 200,
            'clase' => $clase
        ];
        return response() ->json($data);
    }
    public function create(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                    'contenido' => 'required',
                    'fecha' => 'required|date_format:Y-m-d',
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
                $clase = new Clase();
                $clase -> contenido = $request -> contenido;
                $clase -> fecha = $request -> fecha;
                $clase -> ciclo_id = $request -> ciclo_id;
                $clase ->save();
                $data = [
                                'code' => 200,
                                'status' => 'success',
                                'message' => 'Se ha creado correctamente la clase',
                                'clase' => $clase
                            ];
            }
        } else {
            $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Error al crear la clase'
                ];
        }
        return response() ->json($data);
    }

    public function edit(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'contenido' => 'required',
                'fecha' => 'required|date_format:Y-m-d',
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
                $clase = clase::find($request->id);
                if (!empty($clase)) {
                    $clase -> contenido = $request -> contenido;
                    $clase -> fecha = $request -> fecha;
                    $clase -> ciclo_id = $request -> ciclo_id;
                    $clase ->save();
                    $data = [
                                    'code' => 200,
                                    'status' => 'success',
                                    'message' => 'Se ha editado correctamente la clase',
                                    'clase' => $clase
                                ];
                } else {
                    $data = [
                                'code' => 400,
                                'status' => 'error',
                                'message' => 'No existe una clase asociado'
                            ];
                }
            }
        } else {
            $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Error al editar la clase'
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
                    'mensaje' => 'Debe ingresar una clase'
                ];
        } else {
            $clase = Clase::find($request->id);
            if (empty($clase)) {
                $data = [
                        'code' =>400,
                        'status' => 'error',
                        'mensaje' => 'No se encontro la clase'
                    ];
            } else {
                $clase ->delete();
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
