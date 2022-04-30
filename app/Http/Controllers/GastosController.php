<?php

namespace App\Http\Controllers;

use App\Models\Gastos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GastosController extends Controller
{
    public function all()
    {
        /*$gastos = DB::table('gastos as g')->select('g.id','g.valor','g.tipo',
        DB::raw('DATE_FORMAT(g.fecha, "%d-%m-%Y") as fecha'),
        'g.ciclo_id','g.actividad_id','g.competencia_id')->get();*/
        $gastos = Gastos::with('ciclo','actividad','competencia', 'detalles')->get();
        $data = [
            'code' => 200,
            'gastos' => $gastos
        ];
        return response() ->json($data);
    }

    public function create(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'valor' => 'required',
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
                $gastos = new Gastos();
                $gastos -> valor = $request -> valor;
                $gastos -> fecha = $request -> fecha;
                $gastos -> ciclo_id = $request -> ciclo_id;
                $gastos -> actividad_id = $request -> actividad_id;
                $gastos -> competencia_id = $request -> competencia_id;

                $gastos ->save();
                $data = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Se ha creado correctamente el gasto',
                            'gastos' => $gastos
                        ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al crear el gasto'
            ];
        }
        return response() ->json($data);
    }

    public function edit(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'valor' => 'required',
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
                $gastos = Gastos::find($request->id);
                if (!empty($gastos)) {
                    $gastos -> fecha = $request -> fecha;
                    $gastos -> valor = $request -> valor;
                    $gastos -> ciclo_id = $request -> ciclo_id;
                    $gastos -> actividad_id = $request -> actividad_id;
                    $gastos -> competencia_id = $request -> competencia_id;
                    $gastos ->save();
                    $data = [
                                'code' => 200,
                                'status' => 'success',
                                'message' => 'Se ha editado correctamente el gasto',
                                'gastos' => $gastos
                            ];
                } else {
                    $data = [
                            'code' => 401,
                            'status' => 'error',
                            'message' => 'No existe un gasto asociado'
                        ];
                }
            }
        } else {
            $data = [
                'code' => 401,
                'status' => 'error',
                'message' => 'Error al editar el gasto'
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
                'message' => 'Debe ingresar un gasto'
            ];
        } else {
            $gastos = Gastos::find($request->id);
            if (empty($gastos)) {
                $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro un gasto asociado'
                ];
            } else {
                $gastos ->delete();
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
                $gastos = Gastos::with(['detalles'])->firstwhere('id',$request ->id);
                if (empty($gastos)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro el gasto asociado al id'
                ];
                } else {
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'gastos' => $gastos
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al buscar el gastos'
            ];
        }
        return response() -> json($data);
    }
}
