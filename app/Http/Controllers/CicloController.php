<?php

namespace App\Http\Controllers;

use App\Models\Ciclo;
use App\Models\Establecimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
                $ciclo = Ciclo::with('coordinador', 'competencias', 'actividades', 'gastos', 'clases', 'niveles', 'alumnos', 'establecimientos', 'noticias')->firstwhere('id', $request ->id);
                if (empty($ciclo)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro el ciclo asociado al id'
                ];
                } else {
                    $lessons = $ciclo ->clases()->with('nivel')->get();
                    $costs = $ciclo ->gastos()->with('competencia', 'actividad', 'detalles')->get();
                    $studentsEnrolled = $ciclo ->alumnos()->with('establecimiento')->where('participante', 1)->get();
                    $ciclo_id = $ciclo->id;
                    $establecimientos = DB::table('establecimiento')
                    ->whereNotExists(function ($query) use ($ciclo_id) {
                        $query->select('ciclo_establecimiento.*')
                              ->from('ciclo_establecimiento')
                              ->whereColumn('ciclo_establecimiento.establecimiento_id', 'establecimiento.id')->where('ciclo_establecimiento.ciclo_id',$ciclo_id);
                    })
                    ->get();
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'ciclo' => $ciclo,
                    'clases' =>$lessons,
                    'alumnosParticipantes' =>$studentsEnrolled,
                    'gastos' =>$costs,
                    'establecimientosSinCiclo' => $establecimientos
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
                'message' =>'Ingrese fecha de termino'
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
                $ciclo = Ciclo::with('coordinador', 'competencias', 'actividades', 'gastos', 'clases', 'niveles', 'alumnos', 'establecimientos')->firstwhere('id', $ciclo[0]->id);
                $lessons = $ciclo -> clases() -> with('nivel')->get();
                $studentsEnrolled = $ciclo ->alumnos()->with('establecimiento')->where('participante', 1)->get();
                $costs = $ciclo -> gastos()->with('competencia', 'actividad', 'detalles')->get();
                $data = [
                'code' =>200,
                'status' => 'success',
                'ciclo' => $ciclo,
                'clases' =>$lessons,
                'alumnosParticipantes' =>$studentsEnrolled,
                'gastos'=>$costs
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
            'establecimiento_id' => 'required'
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
                    $ciclo -> establecimientos()->detach($request -> establecimiento_id);
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
                    $ciclo -> alumnos()->attach($request -> alumnos_id, ['participante' => false]);
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
                    if (is_array($request->alumnos_id)) {
                        foreach ($request->alumnos_id as  $alumno) {
                            $ciclo->alumnos()->updateExistingPivot($alumno, ['participante' =>$request->participante]);
                        }
                    } else {
                        $ciclo->alumnos()->updateExistingPivot($request->alumnos_id, ['participante' =>$request->participante]);
                    }
                    //$ciclo = Ciclo::with('alumnos')->firstwhere('id', $request->ciclo_id);
                    $data = [
                'code' =>200,
                'status' => 'success',
                //'ciclo' => $ciclo
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
            'alumno_id' => 'required'
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
                    $ciclo -> alumnos()->detach($request -> alumno_id);
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
                    $studentsCandidate = $ciclo ->alumnos()->with('establecimiento:id,nombre')->where('participante', 0)->get();
                    $studentsEnrolled = $ciclo ->alumnos()->with('establecimiento:id,nombre')->where('participante', 1)->get();
                    $establecimientos = DB::table('establecimiento')->select('establecimiento.id', 'establecimiento.nombre', 'ciclo_establecimiento.cupos')
                        ->join('ciclo_establecimiento', 'establecimiento.id', '=', 'ciclo_establecimiento.establecimiento_id')->where('ciclo_establecimiento.ciclo_id', '=', $ciclo->id)->get();
                    $array = array();
                    $establecimientoConAlumnos = [];
                    foreach ($establecimientos as $establecimiento) {
                        $establecimiento_id = $establecimiento->id;
                        foreach ($studentsCandidate as $student) {
                            if ($student->establecimiento_id ==$establecimiento_id) {
                                $array[]= $student;
                            }
                        }
                        $establecimientoarray = (array)$establecimiento;
                        $establecimientoarray['alumnosInscritos'] = $array;
                        $array = array();
                        foreach ($studentsEnrolled as $student) {
                            if ($student->establecimiento_id ==$establecimiento_id) {
                                $array[]= $student;
                            }
                        }
                        $establecimientoarray['alumnosParticipantes'] = $array;
                        array_push($establecimientoConAlumnos, $establecimientoarray);
                        $array = array();
                    }
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'alumnosInscritos' => $studentsCandidate,
                    'alumnosParticipantes' => $studentsEnrolled,
                    'ciclo' => $ciclo,
                    'establecimientos' =>$establecimientoConAlumnos
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
                $studentsCandidate = $ciclo ->alumnos()->with('establecimiento:id,nombre')->where('participante', 0)->get();
                $studentsEnrolled = $ciclo ->alumnos()->with('establecimiento:id,nombre')->where('participante', 1)->get();
                $establecimientos = DB::table('establecimiento')->select('establecimiento.id', 'establecimiento.nombre', 'ciclo_establecimiento.cupos')
                    ->join('ciclo_establecimiento', 'establecimiento.id', '=', 'ciclo_establecimiento.establecimiento_id')->where('ciclo_establecimiento.ciclo_id', '=', $ciclo->id)->get();
                $array = array();
                $establecimientoConAlumnos = [];
                foreach ($establecimientos as $establecimiento) {
                    $establecimiento_id = $establecimiento->id;
                    foreach ($studentsCandidate as $student) {
                        if ($student->establecimiento_id ==$establecimiento_id) {
                            $array[]= $student;
                        }
                    }
                    $establecimientoarray = (array)$establecimiento;
                    $establecimientoarray['alumnosInscritos'] = $array;
                    $array = array();
                    foreach ($studentsEnrolled as $student) {
                        if ($student->establecimiento_id ==$establecimiento_id) {
                            $array[]= $student;
                        }
                    }
                    // $establecimientoarray = (array)$establecimiento;
                    $establecimientoarray['alumnosParticipantes'] = $array;
                    array_push($establecimientoConAlumnos, $establecimientoarray);
                    $array = array();
                }
                $data = [
                'code' =>200,
                'status' => 'success',
                'alumnosInscritos' => $studentsCandidate,
                'alumnosParticipantes' => $studentsEnrolled,
                'ciclo' => $ciclo,
                'establecimientos' =>$establecimientoConAlumnos
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
                $students = $ciclo ->alumnos()->with('establecimiento')->where('participante', 1)->get();
                $ciclo = Ciclo::with('establecimientos') -> get();
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

    //-------------------------------------------------------- METODO PARA OBTENER AYUDANTES Y PROFESORES QUE PARTICIPARON DE UN CICLO

    public function getAssistantsPerCycle(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'ciclo_id' =>'required',
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
                    'message' => 'No se encontro el ciclo'
                ];
                } else {
                    $ayudantes = DB::table('ayudante')->select('ayudante.id', 'ayudante.rut', 'ayudante.apellidos', 'ayudante.nombre', 'ayudante.email')->groupBy('ayudante.id', 'ayudante.rut', 'ayudante.apellidos', 'ayudante.nombre', 'ayudante.email')->join('ayudante_clase', 'ayudante.id', '=', 'ayudante_clase.ayudante_id')
                    ->join('clase', 'ayudante_clase.clase_id', '=', 'clase.id')->where('clase.ciclo_id', '=', $request -> ciclo_id)->get();
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'ayudantes' => $ayudantes,
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al realizar la consulta'
            ];
        }
        return response()-> json($data);
    }

    public function getTeachersPerCycle(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'ciclo_id' =>'required',
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
                    'message' => 'No se encontro el ciclo'
                ];
                } else {
                    $profesores = DB::table('profesor')->select('profesor.id', 'profesor.rut', 'profesor.apellidos', 'profesor.nombre', 'profesor.email', 'profesor.facultad', 'profesor.modalidad')->groupBy('profesor.id', 'profesor.rut', 'profesor.apellidos', 'profesor.nombre', 'profesor.email', 'profesor.facultad', 'profesor.modalidad')
                    ->join('clase_profesor', 'profesor.id', '=', 'clase_profesor.profesor_id')
                    ->join('clase', 'clase_profesor.clase_id', '=', 'clase.id')->where('clase.ciclo_id', '=', $request -> ciclo_id)->get();
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'profesores'=> $profesores
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al realizar la consulta'
            ];
        }
        return response()-> json($data);
    }
}
