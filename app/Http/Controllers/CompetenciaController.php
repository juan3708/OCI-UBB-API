<?php

namespace App\Http\Controllers;

use App\Models\Competencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CompetenciaController extends Controller
{
    public function all()
    {
        $competencia = DB::table('competencia as c')->select('c.id','c.tipo',
        DB::raw('DATE_FORMAT(c.fecha, "%d-%m-%Y") as fecha'),'c.ciclo_id')->get();
        $data = [
            'code' => 200,
            'competencias' => $competencia
        ];
        return response() ->json($data);
    }

    public function create(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'fecha_competencia' => 'required|date_format:Y-m-d',
                'tipo' => 'required',
                'lugar' => 'required',
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
                $competencia = new Competencia();
                $competencia -> fecha_competencia = $request -> fecha_competencia;
                $competencia -> tipo = $request -> tipo;
                $competencia -> lugar = $request -> lugar;
                $competencia -> ciclo_id = $request -> ciclo_id;
                $competencia ->save();
                $data = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Se ha creado correctamente la competencia',
                            'competencia' => $competencia
                        ];
            }
        } else {
            $data = [
                'code' => 401,
                'status' => 'error',
                'message' => 'Error al crear la competencia'
            ];
        }
        return response() ->json($data);
    }

    public function edit(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'fecha_competencia' => 'required|date_format:Y-m-d',
                'tipo' => 'required',
                'lugar' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Ingrese todos los datos porfavor',
                    'errors' => $validate ->errors()
                ];
            } else {
                $competencia = competencia::find($request->id);
                if (!empty($competencia)) {
                    $competencia -> fecha_competencia = $request -> fecha_competencia;
                    $competencia -> tipo = $request -> tipo;
                    $competencia -> lugar = $request -> lugar;
                    $competencia ->save();
                    $data = [
                                'code' => 200,
                                'status' => 'success',
                                'message' => 'Se ha editado correctamente la competencia',
                                'competencia' => $competencia
                            ];
                } else {
                    $data = [
                            'code' => 401,
                            'status' => 'error',
                            'message' => 'No existe una competencias asociada'
                        ];
                }
            }
        } else {
            $data = [
                'code' => 401,
                'status' => 'error',
                'message' => 'Error al editar la competencia'
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
                'message' => 'Debe ingresar una competencia'
            ];
        } else {
            $competencia = Competencia::find($request->id);
            if (empty($competencia)) {
                $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro una competencia'
                ];
            } else {
                $competencia ->delete();
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
                $competencia = Competencia::find($request ->id);
                if (empty($competencia)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro la competencia asociado al id'
                ];
                } else {
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'competencia' => $competencia
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al buscar la competencia'
            ];
        }
        return response() -> json($data);
    }
}
