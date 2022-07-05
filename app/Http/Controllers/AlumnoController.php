<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Ciclo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AlumnoController extends Controller
{
    public function all()
    {

/*
        $alumno = DB::table('alumno as a')->select('a.rut','a.nombre','a.apellidos','a.telefono','a.email','a.curso','a.participante'
        ,'a.direccion','a.telefono_apoderado','a.nombre_apoderado','a.establecimiento_id','a.id',DB::raw('DATE_FORMAT(a.fecha_nacimiento, "%d-%m-%Y") as fecha_nacimiento'))->get();
        */
        $alumno = Alumno::with('establecimiento')->get();
        $data = [
            'code' => 200,
            'alumnos' => $alumno
        ];
        return response() ->json($data);
    }
    public function create(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                    'nombre' => 'required',
                    'apellidos' => 'required',
                    'rut' => 'required|unique:alumno,rut',
                    'telefono' => 'required',
                    'email' => 'required|email:rfc,dns||unique:alumno,email',
                    'fecha_nacimiento' => 'required|date_format:Y-m-d',
                    'curso' => 'required',
                    'direccion' => 'required',
                    'telefono_apoderado' => 'required',
                    'nombre_apoderado' => 'required',
                    'establecimiento_id' =>'required'
                ]);
            if ($validate ->fails()) {
                $data = [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'Ingrese todos los datos porfavor',
                        'errors' => $validate ->errors()
                    ];
            } else {
                if ($this ->valida_rut($request ->rut)) {
                    $alumno = Alumno::where('rut', $request ->rut)->first();
                    if (empty($alumno)) {
                        //$date = \Carbon\Carbon::parse($request ->fecha_nacimiento)->format('Y-m-d');
                        $alumno = new alumno();
                        $alumno -> nombre = $request -> nombre;
                        $alumno -> apellidos = $request -> apellidos;
                        $alumno -> rut = $request -> rut;
                        $alumno -> telefono = $request -> telefono;
                        $alumno -> email = $request -> email;
                        $alumno -> fecha_nacimiento = $request -> fecha_nacimiento;
                        $alumno -> curso = $request -> curso;
                        //$alumno -> participante = $request -> participante;
                        $alumno -> direccion = $request -> direccion;
                        $alumno -> telefono_apoderado = $request -> telefono_apoderado;
                        $alumno -> nombre_apoderado = $request -> nombre_apoderado;
                        $alumno -> establecimiento_id = $request -> establecimiento_id;
                        $alumno ->save();
                        $data = [
                                'code' => 200,
                                'status' => 'success',
                                'message' => 'Se ha creado correctamente el alumno',
                                'alumno' => $alumno
                            ];
                    } else {
                        $data = [
                            'code' => 401,
                            'status' => 'error',
                            'message' => 'Ya existe el alumno'
                        ];
                    }
                } else {
                    $data = [
                        'code' => 401,
                        'status' => 'error',
                        'message' => 'rut invalido'
                    ];
                }
            }
        } else {
            $data = [
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Error al crear el alumno'
                ];
        }
        return response() ->json($data);
    }

    public function edit(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'nombre' => 'required',
                'apellidos' => 'required',
                'rut' => 'required',
                'telefono' => 'required',
                'email' => 'required',
                'fecha_nacimiento' => 'required|date_format:Y-m-d',
                'curso' => 'required',
                'direccion' => 'required',
                'telefono_apoderado' => 'required',
                'nombre_apoderado' => 'required',
                'establecimiento_id' =>'required'
                ]);
            if ($validate ->fails()) {
                $data = [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'Ingrese todos los datos porfavor',
                        'errors' => $validate ->errors()
                    ];
            } else {
                if ($this -> valida_rut($request->rut)) {
                    $alumno = Alumno::find($request->id);
                    if (!empty($alumno)) {
                        //  $date = \Carbon\Carbon::parse($request ->fecha_nacimiento)->format('Y-m-d');
                        $alumno -> nombre = $request -> nombre;
                        $alumno -> apellidos = $request -> apellidos;
                        $alumno -> rut = $request -> rut;
                        $alumno -> telefono = $request -> telefono;
                        $alumno -> email = $request -> email;
                        $alumno -> fecha_nacimiento = $request -> fecha_nacimiento;
                        $alumno -> curso = $request -> curso;
                        $alumno -> direccion = $request -> direccion;
                        $alumno -> telefono_apoderado = $request -> telefono_apoderado;
                        $alumno -> nombre_apoderado = $request -> nombre_apoderado;
                        $alumno -> establecimiento_id = $request -> establecimiento_id;
                        $alumno ->save();
                        $data = [
                                    'code' => 200,
                                    'status' => 'success',
                                    'message' => 'Se ha editado correctamente el alumno',
                                    'alumno' => $alumno
                                ];
                    } else {
                        $data = [
                                'code' => 401,
                                'status' => 'error',
                                'message' => 'No existe un alumno asociado'
                            ];
                    }
                } else {
                    $data = [
                        'code' => 401,
                        'status' => 'error',
                        'message' => 'rut invalido'
                    ];
                }
            }
        } else {
            $data = [
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Error al editar la alumno'
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
                    'message' => 'Debe ingresar un alumno'
                ];
        } else {
            $alumno = Alumno::find($request->id);
            if (empty($alumno)) {
                $data = [
                        'code' =>400,
                        'status' => 'error',
                        'message' => 'No se encontro el alumno'
                    ];
            } else {
                $alumno = Alumno::with('niveles', 'ciclos', 'clases', 'competencias')->firstwhere('id', $request ->id);

                //Detach niveles
                $array_id = array();
                foreach ($alumno->niveles as $key => $nivel) {
                    $array_id[] = $alumno->niveles[$key]->id;
                };
                $alumno -> niveles()-> detach($array_id);

                //Detach ciclos
                $array_id = array();
                foreach ($alumno->ciclos as $key => $ciclo) {
                    $array_id[] = $alumno->ciclos[$key]->id;
                };
                $alumno -> ciclos()-> detach($array_id);

                //Detach clases
                $array_id = array();
                foreach ($alumno->clases as $key => $clase) {
                    $array_id[] = $alumno->clases[$key]->id;
                };
                $alumno -> clases()-> detach($array_id);

                //Detach competencias
                $array_id = array();
                foreach ($alumno->competencias as $key => $competencia) {
                    $array_id[] = $alumno->competencias[$key]->id;
                };
                $alumno -> competencias()-> detach($array_id);
                $alumno ->delete();
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
                $alumno = Alumno::with('establecimiento', 'niveles', 'ciclos', 'clases', 'competencias')->firstwhere('id', $request ->id);
                if (empty($alumno)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro el alumno asociado al id'
                ];
                } else {
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'alumno' => $alumno
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al buscar el alumno'
            ];
        }
        return response() -> json($data);
    }

    public function getStatistic(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'ciclo_id' =>'required',
                'alumno_id' =>'required'
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
                    'message' => 'Seleccione un ciclo'
                ];
                } else {
                    $alumno = Alumno::find($request->alumno_id);
                    if (empty($alumno)) {
                        $data = [
                            'code' =>400,
                            'status' => 'error',
                            'message' => 'No se encontro el alumno asociado al id'
                        ];
                    } else {
                        $Cantassitance = DB::table('alumno_clase')->select(
                            DB::raw('count(case when alumno_clase.asistencia = 1 then 1 else NULL end  ) as asistencias'),
                            DB::raw('count(case when alumno_clase.asistencia = 0 then 1 else NULL end  ) as inasistencias')
                        )->join('clase', 'alumno_clase.clase_id', '=', 'clase.id')
                        ->where('clase.ciclo_id', '=', $request->ciclo_id)->where('alumno_clase.alumno_id', '=', $request->alumno_id)->get();
                        $asistencias = $alumno->clases()->where('ciclo_id', '=', $request->ciclo_id)->get();
                        if (count($asistencias) != 0) {
                            $porcentajeDeAsistencia = ($Cantassitance[0]->asistencias / count($asistencias))*100;
                        } else {
                            $porcentajeDeAsistencia = -1;
                        }
                        $competencias = $alumno->competencias()->where('ciclo_id', '=', $request->ciclo_id)->get();
                        $data = [
                        'code' =>200,
                        //'status' => 'success',
                        //'Alumno' => $alumno,
                        'CantAsistenciasEInasistencias' => $Cantassitance,
                        'Asistencias' => $asistencias,
                        'Porcentaje' =>$porcentajeDeAsistencia,
                        'Competencias'=>$competencias
                    ];
                    }
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

    public function getAssistanceandScoresPerTwoLastCycles(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'alumno_id' =>'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $alumno = Alumno::find($request->alumno_id);
                if (empty($alumno)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro el alumno asociado al id'
                ];
                } else {
                    $ciclos = DB::table('ciclo')->select('ciclo.id', 'ciclo.nombre')->orderBy('id', 'desc')->limit(3)->get();
                    $ciclosConAsistenciaDelAlumno = [];
                    unset($ciclos[0]);
                    foreach ($ciclos as $ciclo) {
                        $cicloArray = (array)$ciclo;
                        $Cantassitance = DB::table('alumno_clase')->select(
                            DB::raw('count(case when alumno_clase.asistencia = 1 then 1 else NULL end  ) as asistencias'),
                            DB::raw('count(case when alumno_clase.asistencia = 0 then 1 else NULL end  ) as inasistencias')
                        )->join('clase', 'alumno_clase.clase_id', '=', 'clase.id')
                        ->where('clase.ciclo_id', '=', $ciclo->id)->where('alumno_clase.alumno_id', '=', $request ->alumno_id)->get();
                        $asistencias = $alumno->clases()->where('ciclo_id', '=', $ciclo->id)->get();
                        $competencias = $alumno->competencias()->where('ciclo_id', '=', $ciclo->id)->get();
                        if (count($asistencias) != 0) {
                            $porcentajeDeAsistencia = ($Cantassitance[0]->asistencias / count($asistencias))*100;
                        } else {
                            $porcentajeDeAsistencia = -1;
                        }
                        $cicloArray['CantAsistenciasEInasistencias'] = $Cantassitance;
                        $cicloArray['Asistencias'] = $asistencias;
                        $cicloArray['PorcentajeAsistencia'] = $porcentajeDeAsistencia;
                        $cicloArray['Competencias'] = $competencias;
                        array_push($ciclosConAsistenciaDelAlumno, $cicloArray);
                    }

                    // $Cantassitance = DB::table('alumno_clase')->select(DB::raw('count(case when alumno_clase.asistencia = 1 then 1 else NULL end  ) as asistencias'),
                    // DB::raw('count(case when alumno_clase.asistencia = 0 then 1 else NULL end  ) as inasistencias'))->join('clase', 'alumno_clase.clase_id','=','clase.id')
                    // ->where('clase.ciclo_id','=',$request->ciclo_id)->where('alumno_clase.alumno_id', '=',$request->alumno_id)->get();
                    // $asistencias = $alumno->clases()->where('ciclo_id','=',$request->ciclo_id)->get();
                    // if (count($asistencias) != 0) {
                    //     $porcentajeDeAsistencia = ($Cantassitance[0]->asistencias / count($asistencias))*100;
                    // }else{
                    //     $porcentajeDeAsistencia = -1;
                    // }
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'CiclosConAsistenciaYCompetencias' => $ciclosConAsistenciaDelAlumno
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

    private function valida_rut($rut)
    {
        if (!preg_match("/^[0-9.]+[-]?+[0-9kK]{1}/", $rut)) {
            return false;
        }

        $rut = preg_replace('/[\.\-]/i', '', $rut);
        $dv = substr($rut, -1);
        $numero = substr($rut, 0, strlen($rut) - 1);
        $i = 2;
        $suma = 0;
        foreach (array_reverse(str_split($numero)) as $v) {
            if ($i == 8) {
                $i = 2;
            }
            $suma += $v * $i;
            ++$i;
        }
        $dvr = 11 - ($suma % 11);

        if ($dvr == 11) {
            $dvr = 0;
        }
        if ($dvr == 10) {
            $dvr = 'K';
        }
        if ($dvr == strtoupper($dv)) {
            return true;
        } else {
            return false;
        }
    }
}
