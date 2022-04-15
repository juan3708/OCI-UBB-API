<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AlumnoController extends Controller
{
    public function all()
    {
        $alumno = DB::table('alumno')->select('*')->get();
        $data = [
            'code' => 200,
            'alumno' => $alumno
        ];
        return response() ->json($data);
    }
    public function create(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                    'nombre' => 'required',
                    'rut' => 'required|unique:alumno,rut',
                    'telefono' => 'required',
                    'email' => 'required|email:rfc,dns||unique:alumno,email',
                    'fecha_nacimiento' => 'required|date_format:y-m-d',
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
                        $alumno = new alumno();
                        $alumno -> nombre = $request -> nombre;
                        $alumno -> rut = $request -> rut;
                        $alumno -> telefono = $request -> telefono;
                        $alumno -> email = $request -> email;
                        $alumno -> fecha_nacimiento = $request -> fecha_nacimiento;
                        $alumno -> curso = $request -> curso;
                        $alumno -> participante = $request -> participante;
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
                    }else{
                        $data = [
                            'code' => 400,
                            'status' => 'error',
                            'message' => 'Ya existe el alumno'
                        ];
                    }
                } else {
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'rut invalido'
                    ];
                }
            }
        } else {
            $data = [
                    'code' => 400,
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
                'rut' => 'required',
                'telefono' => 'required',
                'email' => 'required',
                'fecha_nacimiento' => 'required|date_format:y-m-d',
                'curso' => 'required',
                'participante' => 'required',
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
                        $alumno -> nombre = $request -> nombre;
                        $alumno -> rut = $request -> rut;
                        $alumno -> telefono = $request -> telefono;
                        $alumno -> email = $request -> email;
                        $alumno -> fecha_nacimiento = $request -> fecha_nacimiento;
                        $alumno -> curso = $request -> curso;
                        $alumno -> participante = $request -> participante;
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
                                'code' => 400,
                                'status' => 'error',
                                'message' => 'No existe un alumno asociado'
                            ];
                    }
                } else {
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'rut invalido'
                    ];
                }
            }
        } else {
            $data = [
                    'code' => 400,
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
                    'mensaje' => 'Debe ingresar un alumno'
                ];
        } else {
            $alumno = Alumno::find($request->id);
            if (empty($alumno)) {
                $data = [
                        'code' =>400,
                        'status' => 'error',
                        'mensaje' => 'No se encontro el alumno'
                    ];
            } else {
                $alumno ->delete();
                $data = [
                        'code' =>200,
                        'status' => 'success',
                        'mensaje' => 'Se ha eliminado correctamente'
                    ];
            }
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
