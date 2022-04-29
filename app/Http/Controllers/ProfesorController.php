<?php

namespace App\Http\Controllers;

use App\Models\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProfesorController extends Controller
{
    public function all()
    {
        //$profesor = DB::table('profesor')->select('*')->get();
        $profesor = Profesor::with('clases')->get();
        $data = [
            'code' => 200,
            'profesores' => $profesor
        ];
        return response() ->json($data);
    }

    public function create(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'nombre' => 'required',
                'apellidos' => 'required',
                'email' => 'required|email:rfc,dns|unique:profesor,email',
                'rut' => 'required',
                'facultad' => 'required',
                'modalidad' => 'required',
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
                    $profesor = Profesor::where('rut', $request ->rut)->first();
                    if (empty($profesor)) {
                        $profesor = new profesor();
                        $profesor -> nombre = $request -> nombre;
                        $profesor -> apellidos = $request -> apellidos;
                        $profesor -> email = $request -> email;
                        $profesor -> rut = $request -> rut;
                        $profesor -> facultad = $request -> facultad;
                        $profesor -> modalidad = $request -> modalidad;
                        $profesor ->save();
                        $data = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Se ha creado correctamente el profesor',
                            'profesor' => $profesor
                        ];
                    } else {
                        $data = [
                            'code' => 401,
                            'status' => 'error',
                            'message' => 'Ya existe el profesor'
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
                'message' => 'Error al crear el profesor'
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
                'email' => 'required|email:rfc,dns',
                'rut' => 'required',
                'facultad' => 'required',
                'modalidad' => 'required',
                'id' =>'required'
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
                    $profesor = Profesor::find($request ->id);
                    if (!empty($profesor)) {
                        $profesor -> nombre = $request -> nombre;
                        $profesor -> apellidos = $request -> apellidos;
                        $profesor -> email = $request -> email;
                        $profesor -> rut = $request -> rut;
                        $profesor -> facultad = $request -> facultad;
                        $profesor -> modalidad = $request -> modalidad;
                        $profesor ->save();
                        $data = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Se ha editado correctamente el profesor',
                            'profesor' => $profesor
                        ];
                    } else {
                        $data = [
                            'code' => 401,
                            'status' => 'error',
                            'message' => 'No existe el profesor'
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
                'message' => 'Error al editar el profesor',
                'request' => $request
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
                'message' => 'Debe ingresar un profesor'
            ];
        } else {
                $profesor = Profesor::find($request->id);
                if (empty($profesor)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro el profesor asociado al rut'
                ];
                } else {
                    $profesor -> delete();
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
                $profesor = Profesor::with('clases')->firstwhere('id',$request ->id);
                if (empty($profesor)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro el profesor asociado al id'
                ];
                } else {
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'profesor' => $profesor
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al buscar el profesor'
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
