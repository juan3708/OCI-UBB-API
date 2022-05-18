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
        /*$ciclo = DB::table('ciclo as c')->select('c.id', 'c.anio', 'c.nombre', 'c.presupuesto',
        DB::raw('DATE_FORMAT(c.fecha_inicio, "%d-%m-%Y") as fecha_inicio'),
        DB::raw('DATE_FORMAT(c.fecha_termino, "%d-%m-%Y") as fecha_termino'),
        'coordinador.nombre as nombre_coordinador','coordinador.apellidos as apellidos_coordinador')->join('coordinador','coordinador.id','=','c.coordinador_id')->get();*/
        $ciclo = Ciclo::with('coordinador', 'competencias', 'actividades', 'clases', 'niveles')->get();
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
                'fecha_termino' => 'required|date_format:Y-m-d|after:fecha_inicio',
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
                            'code' => 401,
                            'status' => 'error',
                            'message' => 'Ya existe un ciclo con ese nombre'
                        ];
                }
            }
        } else {
            $data = [
                'code' => 401,
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
                            'code' => 401,
                            'status' => 'error',
                            'message' => 'No existe un ciclo asociado'
                        ];
                }
            }
        } else {
            $data = [
                'code' => 401,
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
                'message' => 'Debe ingresar un ciclo'
            ];
        } else {
            $ciclo = Ciclo::find($request->id);
            if (empty($ciclo)) {
                $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro un ciclo'
                ];
            } else {
                $ciclo ->delete();
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
                $ciclo = Ciclo::with('coordinador', 'competencias', 'actividades', 'gastos', 'clases', 'niveles', 'alumnos', 'establecimientos')->firstwhere('id', $request ->id);
                if (empty($ciclo)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro el ciclo asociado al id'
                ];
                } else {
                    $lessons = $ciclo ->clases()->with('nivel')->get();
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'ciclo' => $ciclo,
                    'clases' =>$lessons
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al buscar el ciclo'
            ];
        }
        return response() -> json($data);
    }

    public function getCyclePerFinishDate(Request $request)
    {
        if ($request -> fecha_termino  == '') {
            $data = [
                'code' =>400,
                'status' => 'error',
            ];
        } else {
            $ciclo = DB::table('ciclo')->where('fecha_termino', '>=', $request -> fecha_termino) ->orWhere('id', '=', DB::table('ciclo')->max('id'))->limit(1)->get();

            if($ciclo ->isEmpty()){                    
                $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'No se encontro el ciclo asociado al id'
            ];}else{
                $ciclo = Ciclo::with('coordinador', 'competencias', 'actividades', 'gastos', 'clases', 'niveles', 'alumnos', 'establecimientos')->firstwhere('id', $ciclo[0]->id);
                $lessons = $ciclo -> clases() -> with('nivel')->get();
                
                $data = [
                'code' =>200,
                'status' => 'success',
                'ciclo' => $ciclo,
                'clases' =>$lessons
            ];
            }
        }
        return response()->json($data);
    }

    //  ------------------------------ RELACION CICLO ESTABLECIMIENTO --------------------------------

    public function CycleHasEstablishments(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
            'ciclo_id' =>'required',
            'establecimientos_id' => 'required'
        ]);
            if ($validate ->fails()) {
                $data = [
                'code' => 400,
                'status' => 'error',
                'errors' => $validate ->errors()
            ];
            } else {
                $ciclo = Ciclo::find($request ->ciclo_id);
                if (empty($ciclo)) {
                    $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'No se encontro la ciclo'
            ];
                } else {
                    $ciclo -> establecimientos()->attach($request -> establecimientos_id, ['cupos'=>0]);
                    $ciclo = Ciclo::with('establecimientos')->firstwhere('id', $request->ciclo_id);
                    $data = [
                'code' =>200,
                'status' => 'success',
                'ciclo' => $ciclo
            ];
                }
            }
        } else {
            $data = [
            'code' =>400,
            'status' => 'error',
            'message' => 'Error al asociar ciclo con establecimientos'
        ];
        }
        return response()-> json($data);
    }

    public function UpdateEstablishments(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
            'ciclo_id' =>'required',
            'establecimientos_id' => 'required',
            "cupos" => 'required',
        ]);
            if ($validate ->fails()) {
                $data = [
                'code' => 400,
                'status' => 'error',
                'errors' => $validate ->errors()
            ];
            } else {
                $ciclo = Ciclo::find($request ->ciclo_id);
                if (empty($ciclo)) {
                    $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'No se encontro la ciclo'
            ];
                } else {
                    foreach ($request->establecimientos_id as $key => $establecimiento) {
                        $ciclo->establecimientos()->updateExistingPivot($establecimiento, ['cupos' =>$request->cupos[$key]]);
                    }
                    $ciclo = Ciclo::with('establecimientos')->firstwhere('id', $request->ciclo_id);
                    $data = [
                'code' =>200,
                'status' => 'success',
                'ciclo' => $ciclo
            ];
                }
            }
        } else {
            $data = [
            'code' =>400,
            'status' => 'error',
            'message' => 'Error al asociar ciclo con establecimientos'
        ];
        }
        return response()-> json($data);
    }

    public function deleteEstablishmentPerCycle(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
            'ciclo_id' =>'required',
            'establecimientos_id' => 'required'
        ]);
            if ($validate ->fails()) {
                $data = [
                'code' => 400,
                'status' => 'error',
                'errors' => $validate ->errors()
            ];
            } else {
                $ciclo = Ciclo::find($request ->ciclo_id);
                if (empty($ciclo)) {
                    $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'No se encontro la ciclo'
            ];
                } else {
                    $ciclo -> establecimientos()->detach($request -> establecimientos_id);
                    $ciclo = Ciclo::with('establecimientos')->firstwhere('id', $request->ciclo_id);
                    $data = [
                'code' =>200,
                'status' => 'success',
                'ciclo' => $ciclo
            ];
                }
            }
        } else {
            $data = [
            'code' =>400,
            'status' => 'error',
            'message' => 'Error al asociar ciclo con establecimientos'
        ];
        }
        return response()-> json($data);
    }


    //---------------------------------------------- RELACION CICLO ALUMNO ------------------------------------------------------------


    public function CycleHasStudents(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
            'ciclo_id' =>'required',
            'alumnos_id' => 'required'
        ]);
            if ($validate ->fails()) {
                $data = [
                'code' => 400,
                'status' => 'error',
                'errors' => $validate ->errors()
            ];
            } else {
                $ciclo = Ciclo::find($request ->ciclo_id);
                if (empty($ciclo)) {
                    $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'No se encontro la ciclo'
            ];
                } else {
                    $ciclo -> alumnos()->attach($request -> alumnos_id, ['inscrito'=>true, 'participante' => false]);
                    $ciclo = Ciclo::with('alumnos')->firstwhere('id', $request->ciclo_id);
                    $data = [
                'code' =>200,
                'status' => 'success',
                'ciclo' => $ciclo
            ];
                }
            }
        } else {
            $data = [
            'code' =>400,
            'status' => 'error',
            'message' => 'Error al asociar ciclo con alumnos'
        ];
        }
        return response()-> json($data);
    }

    public function UpdateCandidates(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
            'ciclo_id' =>'required',
            'alumnos_id' => 'required',
            'participante' => 'required',
        ]);
            if ($validate ->fails()) {
                $data = [
                'code' => 400,
                'status' => 'error',
                'errors' => $validate ->errors()
            ];
            } else {
                $ciclo = Ciclo::find($request ->ciclo_id);
                if (empty($ciclo)) {
                    $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'No se encontro la ciclo'
            ];
                } else {
                    foreach ($request->alumnos_id as $key => $alumno) {
                        $ciclo->alumnos()->updateExistingPivot($alumno, ['participante' =>$request->participante[$key]]);
                    }
                    $ciclo = Ciclo::with('alumnos')->firstwhere('id', $request->ciclo_id);
                    $data = [
                'code' =>200,
                'status' => 'success',
                'ciclo' => $ciclo
            ];
                }
            }
        } else {
            $data = [
            'code' =>400,
            'status' => 'error',
            'message' => 'Error al asociar ciclo con alumnos'
        ];
        }
        return response()-> json($data);
    }

    public function Updateenrolled(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
            'ciclo_id' =>'required',
            'alumnos_id' => 'required',
            'inscrito' => 'required',
        ]);
            if ($validate ->fails()) {
                $data = [
                'code' => 400,
                'status' => 'error',
                'errors' => $validate ->errors()
            ];
            } else {
                $ciclo = Ciclo::find($request ->ciclo_id);
                if (empty($ciclo)) {
                    $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'No se encontro la ciclo'
            ];
                } else {
                    foreach ($request->alumnos_id as $key => $establecimiento) {
                        $ciclo->alumnos()->updateExistingPivot($establecimiento, ['inscrito' =>$request->inscrito[$key]]);
                    }
                    $ciclo = Ciclo::with('alumnos')->firstwhere('id', $request->ciclo_id);
                    $data = [
                'code' =>200,
                'status' => 'success',
                'ciclo' => $ciclo
            ];
                }
            }
        } else {
            $data = [
            'code' =>400,
            'status' => 'error',
            'message' => 'Error al asociar ciclo con alumnos'
        ];
        }
        return response()-> json($data);
    }

    public function deleteStudentsPerCycle(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
            'ciclo_id' =>'required',
            'alumnos_id' => 'required'
        ]);
            if ($validate ->fails()) {
                $data = [
                'code' => 400,
                'status' => 'error',
                'errors' => $validate ->errors()
            ];
            } else {
                $ciclo = Ciclo::find($request ->ciclo_id);
                if (empty($ciclo)) {
                    $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'No se encontro la ciclo'
            ];
                } else {
                    $ciclo -> alumnos()->detach($request -> alumnos_id);
                    $ciclo = Ciclo::with('alumnos')->firstwhere('id', $request->ciclo_id);
                    $data = [
                'code' =>200,
                'status' => 'success',
                'ciclo' => $ciclo
            ];
                }
            }
        } else {
            $data = [
            'code' =>400,
            'status' => 'error',
            'message' => 'Error al asociar ciclo con alumnos'
        ];
        }
        return response()-> json($data);
    }
    // METODO PARA OBTENER LOS ALUMNOS QUE SE INSCRIBIERON A LAS OCI.
    public function getStudentsCandidatePerCycle(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'ciclo_id' =>'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $ciclo = Ciclo::find($request -> ciclo_id);
                if (empty($ciclo)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro el ciclo asociado al id'
                ];
                } else {
                    $students = $ciclo ->alumnos()->with('establecimiento')->where('inscrito', 1)->get();
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'alumnos' => $students,
                    'ciclo' => $ciclo
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al buscar el ciclo'
            ];
        }
        return response() -> json($data);
    }

    // METODO PARA OBTENER LOS ALUMNOS QUE SE INSCRIBIERON A LAS OCI MEDIANTE LA FECHA DE TERMINO.
    public function getStudentsCandidatePerCyclePerFinishDate(Request $request)
    {
        if ($request -> fecha_termino  == '') {
            $data = [
                'code' =>400,
                'status' => 'error',
            ];
        } else {
            $ciclo = DB::table('ciclo')->where('fecha_termino', '>=', $request -> fecha_termino) ->orWhere('id', '=', DB::table('ciclo')->max('id'))->limit(1)->get();
            if ($ciclo ->isEmpty()) {
                $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'No se encontro el ciclo asociado al id'
            ];
            } else {
                $ciclo = Ciclo::find($ciclo[0]->id);
                $students = $ciclo ->alumnos()->with('establecimiento')->where('inscrito', 1)->get();
                $data = [
                'code' =>200,
                'status' => 'success',
                'alumnos' => $students,
                'ciclo' => $ciclo
            ];
            }
        }
        return response()->json($data);
    }

    // METODO PARA OBTENER LOS ALUMNOS QUE PARTICIPARAN DE LAS OCI.
    public function getStudentsEnrolledPerCycle(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'ciclo_id' =>'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $ciclo = Ciclo::find($request -> ciclo_id);
                if (empty($ciclo)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro el ciclo asociado al id'
                ];
                } else {
                    $students = $ciclo ->alumnos()->with('establecimiento')->where('participante', 1)->get();
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'alumnos' => $students,
                    'ciclo' => $ciclo
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al buscar el ciclo'
            ];
        }
        return response() -> json($data);
    }

    // METODO PARA OBTENER LOS ALUMNOS QUE SE INSCRIBIERON A LAS OCI MEDIANTE LA FECHA DE TERMINO.
    public function getStudentsEnrolledPerCyclePerFinishDate(Request $request)
    {
        if ($request -> fecha_termino  == '') {
            $data = [
                        'code' =>400,
                        'status' => 'error',
                    ];
        } else {
            $ciclo = DB::table('ciclo')->where('fecha_termino', '>=', $request -> fecha_termino) ->orWhere('id', '=', DB::table('ciclo')->max('id'))->limit(1)->get();
            if ($ciclo ->isEmpty()) {
                $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'No se encontro el ciclo asociado al id'
            ];
            } else {
                $ciclo = Ciclo::find($ciclo[0]->id);
                $students = $ciclo ->alumnos()->with('establecimiento')->where('postulante', 1)->get();
                $data = [
                        'code' =>200,
                        'status' => 'success',
                        'alumnos' => $students,
                        'ciclo' => $ciclo
                    ];
            }
        }
        return response()->json($data);
    }
}
