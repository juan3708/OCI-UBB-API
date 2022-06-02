<?php

namespace App\Http\Controllers;

use App\Models\Clase;
use Countable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClaseController extends Controller
{
    public function all()
    {
        /*$clase = DB::table('clase as c')->select('c.id','c.contenido',
        DB::raw('DATE_FORMAT(c.fecha, "%d-%m-%Y") as fecha'),'c.ciclo_id')->get();*/
        $clase = Clase::with('ciclo', 'nivel')->get();
        
        $data = [
            'code' => 200,
            'clases' => $clase
        ];
        return response() ->json($data);
    }
    public function create(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                    'contenido' => 'required',
                    'fecha' => 'required|date_format:Y-m-d',
                    'ciclo_id' => 'required',
                    'nivel_id' => 'required'
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
                $clase -> nivel_id = $request -> nivel_id;

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
                    'code' => 401,
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
                'ciclo_id' => 'required',
                'nivel_id' => 'required'
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
                    $clase -> nivel_id = $request -> nivel_id;
                    $clase ->save();
                    $data = [
                                    'code' => 200,
                                    'status' => 'success',
                                    'message' => 'Se ha editado correctamente la clase',
                                    'clase' => $clase
                                ];
                } else {
                    $data = [
                                'code' => 401,
                                'status' => 'error',
                                'message' => 'No existe una clase asociado'
                            ];
                }
            }
        } else {
            $data = [
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Error al editar la clase'
                ];
        }
        return response() ->json($data);
    }

    public function delete(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'clase_id' => 'required'
                ]);
            if ($validate ->fails()) {
                $data = [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'Ingrese todos los datos porfavor',
                        'errors' => $validate ->errors()
                    ];
            } else {
                $clase = Clase::find($request->clase_id);
                if (empty($clase)) {
                    $data = [
                        'code' =>400,
                        'status' => 'error',
                        'message' => 'No se encontro la clase'
                    ];
                } else {
                    $clase = Clase::with('alumnos', 'ayudantes', 'profesores')->firstwhere('id', $request ->clase_id);
                    //Detach alumnos
                    $array_id = array();
                    foreach ($clase->alumnos as $key => $alumno) {
                        $array_id[] = $clase->alumnos[$key]->id;
                    };
                    $clase -> alumnos()-> detach($array_id);

                    //Detach ayudantes
                    $array_id = array();
                    foreach ($clase->ayudantes as $key => $ayudante) {
                        $array_id[] = $clase->ayudantes[$key]->id;
                    };
                    $clase -> ayudantes()-> detach($array_id);

                    //Detach profesores
                    $array_id = array();
                    foreach ($clase->profesores as $key => $profesor) {
                        $array_id[] = $clase->profesores[$key]->id;
                    };
                    $clase -> profesores()-> detach($array_id);
                    $clase ->delete();
                    $data = [
                        'code' =>200,
                        'status' => 'success',
                        'message' => 'Se ha eliminado correctamente'
                    ];
                }
            }
        } else {
            $data = [
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Error al eliminar la clase'
                ];
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
                $clase = Clase::with('ciclo', 'nivel', 'alumnos', 'ayudantes', 'profesores')->firstwhere('id', $request ->id);
                if (empty($clase)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro la clase asociada al id'
                ];
                } else {
                    $clase_id = $request->id;
                    $studentWithoutLesson = DB::table('alumno')->whereNotExists(function ($query) use ($clase_id) {
                        $query -> from('alumno_clase')->select('alumno_clase.alumno_id')->whereColumn('alumno_clase.alumno_id', '=', 'alumno.id', 'and', 'alumno_clase.clase_id', '=', $clase_id);
                    })->get();
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'clase' => $clase,
                    'alumnosSinClase' => $studentWithoutLesson
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al buscar la clase'
            ];
        }
        return response() -> json($data);
    }

    // ------------------------ METODOS RELACION ALUMNO CLASE ------------------------------

    public function LessonHasStudents(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'clase_id' =>'required',
                'alumnos_id' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $clase = Clase::find($request ->clase_id);
                if (empty($clase)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro la clase'
                ];
                } else {
                    $clase -> alumnos()->attach($request -> alumnos_id, ['asistencia'=>false]);
                    $clase = clase::with('alumnos')->firstwhere('id', $request->clase_id);
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'clase' => $clase
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al asociar clase con alumnos'
            ];
        }
        return response()-> json($data);
    }

    public function UpdateListLesson(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'clase_id' =>'required',
                'alumnos_id' => 'required',
                "asistencias" => 'required',
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $clase = Clase::find($request ->clase_id);
                if (empty($clase)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro la clase'
                ];
                } else {
                    foreach ($request->alumnos_id as $key => $alumno) {
                        $clase->alumnos()->updateExistingPivot($alumno, ['asistencia' =>$request->asistencias[$key]]);
                    }
                    $clase = clase::with('alumnos')->firstwhere('id', $request->clase_id);
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'clase' => $clase
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al asociar clase con alumnos'
            ];
        }
        return response()-> json($data);
    }

    public function deleteStudentPerLesson(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'clase_id' =>'required',
                'alumnos_id' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $clase = Clase::find($request ->clase_id);
                if (empty($clase)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro la clase'
                ];
                } else {
                    $clase -> alumnos()->detach($request -> alumnos_id);
                    $clase = clase::with('alumnos')->firstwhere('id', $request->clase_id);
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'clase' => $clase
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al asociar clase con alumnos'
            ];
        }
        return response()-> json($data);
    }

    // ------------------------ METODOS RELACION PROFESOR CLASE ------------------------------
    
    public function LessonHasTeachers(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'clase_id' =>'required',
                'profesores_id' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $clase = Clase::find($request ->clase_id);
                if (empty($clase)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro la clase'
                ];
                } else {
                    $clase -> profesores()->attach($request -> profesores_id);
                    $clase = Clase::with('profesores')->firstwhere('id', $request->clase_id);
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'clase' => $clase
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al asociar clase con profesores'
            ];
        }
        return response()-> json($data);
    }

    public function deleteTeachersPerLesson(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'clase_id' =>'required',
                'profesores_id' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $clase = Clase::find($request ->clase_id);
                if (empty($clase)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro la clase'
                ];
                } else {
                    $clase -> profesores()->detach($request -> profesores_id);
                    $clase = Clase::with('profesores')->firstwhere('id', $request->clase_id);
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'clase' => $clase
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al eliminar profesor relacionado con clase'
            ];
        }
        return response()-> json($data);
    }

    // ------------------------ METODOS RELACION AYUDANTE CLASE ------------------------------

    public function LessonHasAssistants(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'clase_id' =>'required',
                'ayudantes_id' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $clase = Clase::find($request ->clase_id);
                if (empty($clase)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro la clase'
                ];
                } else {
                    $clase -> ayudantes()->attach($request -> ayudantes_id);
                    $clase = Clase::with('ayudantes')->firstwhere('id', $request->clase_id);
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'clase' => $clase
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al asociar clase con ayudantes'
            ];
        }
        return response()-> json($data);
    }

    public function deleteAssistantsPerLesson(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'clase_id' =>'required',
                'ayudantes_id' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $clase = Clase::find($request ->clase_id);
                if (empty($clase)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro la clase'
                ];
                } else {
                    $clase -> ayudantes()->detach($request -> ayudantes_id);
                    $clase = clase::with('ayudantes')->firstwhere('id', $request->clase_id);
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'clase' => $clase
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al eliminar relacion ayudante con clase'
            ];
        }
        return response()-> json($data);
    }


    //-------------------------------------------- METODOS RELACION CLASE PROFESOR AYUDANTE ---------------------------------------------------------

    public function getAssistantsAndTeacherWhereNotExist(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'clase_id' =>'required',
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $clase = Clase::find($request ->clase_id);
                if (empty($clase)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro la clase'
                ];
                } else {
                    $clase_id = $request->clase_id;
                    $ayudantes = DB::table('ayudante')->whereNotExists(function ($query) use ($clase_id) {
                        $query -> from('ayudante_clase')->select('ayudante_clase.ayudante_id')->whereColumn('ayudante_clase.ayudante_id', '=', 'ayudante.id', 'and', 'ayudante_clase.clase_id', '=', $clase_id);
                    })->get();
                    $profesores = DB::table('profesor')->whereNotExists(function ($query) use ($clase_id) {
                        $query -> from('clase_profesor')->select('clase_profesor.profesor_id')->whereColumn('clase_profesor.profesor_id', '=', 'profesor.id', 'and', 'clase_profesor.clase_id', '=', $clase_id);
                    })->get();
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'ayudantes' => $ayudantes,
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
