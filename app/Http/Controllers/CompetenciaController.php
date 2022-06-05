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
        /* $competencia = DB::table('competencia as c')->select('c.id','c.tipo',
         DB::raw('DATE_FORMAT(c.fecha, "%d-%m-%Y") as fecha'),'c.ciclo_id')->get();*/
        $competencia = Competencia::with('gastos', 'ciclo')->get();
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
                'fecha' => 'required|date_format:Y-m-d',
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
                $competencia -> fecha = $request -> fecha;
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
                'fecha' => 'required|date_format:Y-m-d',
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
                    $competencia -> fecha = $request -> fecha;
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
                $array_id = array();
                foreach ($competencia->alumnos as $key => $alumno) {
                    $array_id[] = $competencia->alumnos[$key]->id;
                };
                $competencia -> alumnos()-> detach($array_id);
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
                $competencia = Competencia::with('alumnos', 'gastos')->firstwhere('id', $request ->id);
                if (empty($competencia)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro la competencia asociado al id'
                ];
                } else {
                $gastos = $competencia ->gastos()->with('detalles')->get();
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'competencia' => $competencia,
                    'gastos' => $gastos
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

//------------------------------------------------------ RELACION COMPETENCIA ALUMNO -------------------------------------------------------------
    
    public function competitionHasStudent(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'competencia_id' =>'required',
                'alumnos_id' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $competencia = Competencia::find($request ->competencia_id);
                if (empty($competencia)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro la competencia'
                ];
                } else {
                    $competencia -> alumnos()->attach($request -> alumnos_id,['puntaje'=>0]);
                    $data = [
                    'code' =>200,
                    'status' => 'success'
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al asociar competencia con alumnos'
            ];
        }
        return response()-> json($data);
    }

    public function deleteStudentsPerCompetition(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'competencia_id' =>'required',
                'alumno_id' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $competencia = Competencia::find($request ->competencia_id);
                if (empty($competencia)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro la competencia'
                ];
                } else {
                    $competencia -> alumnos()->detach($request -> alumno_id);
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al asociar competencia con alumnos'
            ];
        }
        return response()-> json($data);
    }

    public function editScorePerStudent(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'competencia_id' =>'required',
                'alumnos_id' => 'required',
                "puntajes" => 'required',
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $competencia = competencia::find($request ->competencia_id);
                if (empty($competencia)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro la competencia'
                ];
                } else {
                    foreach ($request->alumnos_id as $key => $alumno) {
                        $competencia->alumnos()->updateExistingPivot($alumno, ['puntaje' =>$request->puntajes[$key]]);
                    }
                    $competencia = Competencia::with('alumnos')->firstwhere('id', $request->competencia_id);
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
                'message' => 'Error al asignar puntaje a alumno'
            ];
        }
        return response()-> json($data);
    }
}
