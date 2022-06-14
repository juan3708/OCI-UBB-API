<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
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
        $ciclo = Ciclo::with('coordinador', 'competencias', 'actividades', 'clases', 'niveles','establecimientos')->get();
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
                    $lessons = $ciclo ->clases()->with('ciclo', 'nivel', 'alumnos', 'ayudantes', 'profesores')->get();
                    $costs = $ciclo ->gastos()->with('competencia', 'actividad', 'detalles')->get();
                    $studentsEnrolled = $ciclo ->alumnos()->with('establecimiento')->where('participante', 1)->get();
                    $ciclo_id = $ciclo->id;
                    $competencias = $ciclo->competencias()->with('alumnos', 'gastos')->get();
                    $actividades = $ciclo->actividades()->with('gastos')->get();
                    $establecimientos = DB::table('establecimiento')
                    ->whereNotExists(function ($query) use ($ciclo_id) {
                        $query->select('ciclo_establecimiento.*')
                              ->from('ciclo_establecimiento')
                              ->whereColumn('ciclo_establecimiento.establecimiento_id', 'establecimiento.id')->where('ciclo_establecimiento.ciclo_id', $ciclo_id);
                    })
                    ->get();
                    $niveles = $ciclo->niveles()->with('clases', 'alumnos')->get();
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'ciclo' => $ciclo,
                    'clases' =>$lessons,
                    'alumnosParticipantes' =>$studentsEnrolled,
                    'gastos' =>$costs,
                    'competencias'=>$competencias,
                    'establecimientosSinCiclo' => $establecimientos,
                    'niveles' => $niveles,
                    'actividades' => $actividades
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


    // -------------------------------------- METODOS RELACIONADOS A LAS ESTADISTICAS DE ALUMNO/ESTABLECIMIENTOS/CICLO ----------------------

    public function getAssistancePerDateAndCycle(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'ciclo_id' =>'required',
                'fecha_inicial' => 'required|date_format:Y-m-d',
                'fecha_final' => 'required|date_format:Y-m-d|after:fecha_inicial'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $ciclo = Ciclo::find($request->ciclo_id);
                if (empty($ciclo)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro el ciclo asociado al id'
                ];
                } else {
                    $students = DB::table('alumno')->select('alumno.*')->join('alumno_clase', 'alumno.id', '=', 'alumno_clase.alumno_id')
                    ->join('clase', 'alumno_clase.clase_id', '=', 'clase.id')->where('clase.ciclo_id', '=', $request->ciclo_id)
                    ->whereBetween('clase.fecha', [$request->fecha_inicial, $request->fecha_final])->distinct()->get();
                    $studentsWithAssistanceStatistic = array();
                    foreach ($students as $student) {
                        $studentArray = (array)$student;
                        $student = Alumno::find($student->id);
                        $Cantassitance = DB::table('alumno_clase')->select(
                            DB::raw('count(case when alumno_clase.asistencia = 1 then 1 else NULL end  ) as asistencias'),
                            DB::raw('count(case when alumno_clase.asistencia = 0 then 1 else NULL end  ) as inasistencias')
                        )->join('clase', 'alumno_clase.clase_id', '=', 'clase.id')
                        ->where('clase.ciclo_id', '=', $request->ciclo_id)->where('alumno_clase.alumno_id', '=', $student->id)->whereBetween('clase.fecha', [$request->fecha_inicial, $request->fecha_final])
                        ->distinct()->get();
                        $asistencias = $student->clases()->where('ciclo_id', '=', $ciclo->id)->whereBetween('fecha', [$request->fecha_inicial, $request->fecha_final])->get();
                        //$competecias = $student->competencias()->where('ciclo_id','=',$ciclo->id)->get();
                        if (count($asistencias) != 0) {
                            $porcentajeDeAsistencia = ($Cantassitance[0]->asistencias / count($asistencias))*100;
                        } else {
                            $porcentajeDeAsistencia = -1;
                        }
                        $studentArray['CantAsistenciasEInasistencias'] = $Cantassitance;
                        $studentArray['Asistencias'] = $asistencias;
                        $studentArray['PorcentajeAsistencia'] = $porcentajeDeAsistencia;
                        //$studentArray['Competencias'] = $competecias;
                        array_push($studentsWithAssistanceStatistic, $studentArray);
                    }
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'estudiantesConEstadisticaDeAsistencia' => $studentsWithAssistanceStatistic,
                    //'Asistencias' => $asistencias,
                    //'Porcentaje' =>$porcentajeDeAsistencia
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
        return response() -> json($data);
    }


    public function getStudentAssistancePerCycleAndEstablishment(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'ciclo_id' =>'required',
                'establecimiento_id' => 'required',
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $ciclo = Ciclo::find($request->ciclo_id);
                if (empty($ciclo)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro el ciclo asociado al id'
                ];
                } else {
                    $students = DB::table('alumno')->select('alumno.*')->join('alumno_clase', 'alumno.id', '=', 'alumno_clase.alumno_id')
                    ->join('clase', 'alumno_clase.clase_id', '=', 'clase.id')->where('clase.ciclo_id', '=', $request->ciclo_id)
                    ->where('alumno.establecimiento_id', '=', $request->establecimiento_id)->distinct()->get();
                    $studentsWithAssistanceStatistic = array();
                    foreach ($students as $student) {
                        $studentArray = (array)$student;
                        $student = Alumno::find($student->id);
                        $Cantassitance = DB::table('alumno_clase')->select(
                            DB::raw('count(case when alumno_clase.asistencia = 1 then 1 else NULL end  ) as asistencias'),
                            DB::raw('count(case when alumno_clase.asistencia = 0 then 1 else NULL end  ) as inasistencias')
                        )->join('clase', 'alumno_clase.clase_id', '=', 'clase.id')
                        ->where('clase.ciclo_id', '=', $request->ciclo_id)->where('alumno_clase.alumno_id', '=', $student->id)->distinct()->get();
                        $asistencias = $student->clases()->where('ciclo_id', '=', $ciclo->id)->get();
                        //$competecias = $student->competencias()->where('ciclo_id','=',$ciclo->id)->get();
                        if (count($asistencias) != 0) {
                            $porcentajeDeAsistencia = ($Cantassitance[0]->asistencias / count($asistencias))*100;
                        } else {
                            $porcentajeDeAsistencia = -1;
                        }
                        $studentArray['CantAsistenciasEInasistencias'] = $Cantassitance;
                        $studentArray['Asistencias'] = $asistencias;
                        $studentArray['PorcentajeAsistencia'] = $porcentajeDeAsistencia;
                        //$studentArray['Competencias'] = $competecias;
                        array_push($studentsWithAssistanceStatistic, $studentArray);
                    }
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'estudiantesConEstadisticaDeAsistencia' => $studentsWithAssistanceStatistic,
                    //'Asistencias' => $asistencias,
                    //'Porcentaje' =>$porcentajeDeAsistencia
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
        return response() -> json($data);
    }

    public function getStudentAssistancePerCycle(Request $request)
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
                $ciclo = Ciclo::find($request->ciclo_id);
                if (empty($ciclo)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro el ciclo asociado al id'
                ];
                } else {
                    $establecimientos = $ciclo->establecimientos()->select('establecimiento.id','nombre','direccion','telefono_profesor','email_profesor','nombre_profesor',
                    'email','telefono')->get();
                    $establecimientoWithStudentsStatistic = array();
                    foreach ($establecimientos as $establecimiento) {
                        $establecimientoArray = $establecimiento->toArray();
                        $students = DB::table('alumno')->select('alumno.*')->join('alumno_clase', 'alumno.id', '=', 'alumno_clase.alumno_id')
                        ->join('clase', 'alumno_clase.clase_id', '=', 'clase.id')->where('clase.ciclo_id', '=', $request->ciclo_id)
                        ->where('alumno.establecimiento_id', '=', $establecimiento->id)->distinct()->get();
                        $studentsWithAssistanceStatistic = array();
                        foreach ($students as $student) {
                            $studentArray = (array)$student;
                            $student = Alumno::find($student->id);
                            $Cantassitance = DB::table('alumno_clase')->select(
                                DB::raw('count(case when alumno_clase.asistencia = 1 then 1 else NULL end  ) as asistencias'),
                                DB::raw('count(case when alumno_clase.asistencia = 0 then 1 else NULL end  ) as inasistencias')
                            )->join('clase', 'alumno_clase.clase_id', '=', 'clase.id')
                            ->where('clase.ciclo_id', '=', $request->ciclo_id)->where('alumno_clase.alumno_id', '=', $student->id)->distinct()->get();
                            $asistencias = $student->clases()->where('ciclo_id', '=', $ciclo->id)->get();
                            //$competecias = $student->competencias()->where('ciclo_id','=',$ciclo->id)->get();
                            if (count($asistencias) != 0) {
                                $porcentajeDeAsistencia = ($Cantassitance[0]->asistencias / count($asistencias))*100;
                            } else {
                                $porcentajeDeAsistencia = -1;
                            }
                            $studentArray['CantAsistenciasEInasistencias'] = $Cantassitance;
                            $studentArray['Asistencias'] = $asistencias;
                            $studentArray['PorcentajeAsistencia'] = $porcentajeDeAsistencia;
                            //$studentArray['Competencias'] = $competecias;
                            array_push($studentsWithAssistanceStatistic, $studentArray);
                        }
                        $establecimientoArray['alumnos'] = $studentsWithAssistanceStatistic;
                        array_push($establecimientoWithStudentsStatistic, $establecimientoArray);
                    }
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'Establecimientos' => $establecimientoWithStudentsStatistic,
                    //'Asistencias' => $asistencias,
                    //'Porcentaje' =>$porcentajeDeAsistencia
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
        return response() -> json($data);
    }
}
