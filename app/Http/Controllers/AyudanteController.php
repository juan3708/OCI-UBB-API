<?php

namespace App\Http\Controllers;

use App\Models\Ayudante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AyudanteController extends Controller
{
    public function all()
    {
        $ayudante = DB::table('ayudante')->select('*')->get();
        $data = [
            'code' => 200,
            'ayudantees' => $ayudante
        ];
        return response() ->json($data);
    }

    public function create(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'nombre' => 'required',
                'apellidos' => 'required',
                'email' => 'required',
                'rut' => 'required'
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
                    $ayudante = Ayudante::where('rut', $request ->rut)->first();
                    if (empty($ayudante)) {
                        $ayudante = new ayudante();
                        $ayudante -> nombre = $request -> nombre;
                        $ayudante -> apellidos = $request -> apellidos;
                        $ayudante -> email = $request -> email;
                        $ayudante -> rut = $request -> rut;
                        $ayudante ->save();
                        $data = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Se ha creado correctamente el ayudante',
                            'ayudante' => $ayudante
                        ];
                    } else {
                        $data = [
                            'code' => 400,
                            'status' => 'error',
                            'message' => 'Ya existe el ayudante'
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
                'message' => 'Error al crear el Ayudante'
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
                'email' => 'required',
                'rut' => 'required',
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
                    $ayudante = ayudante::find($request ->id);
                    if (!empty($ayudante)) {
                        $ayudante -> nombre = $request -> nombre;
                        $ayudante -> apellidos = $request -> apellidos;
                        $ayudante -> email = $request -> email;
                        $ayudante -> rut = $request -> rut;
                        $ayudante ->save();
                        $data = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Se ha editado correctamente el ayudante',
                            'ayudante' => $ayudante
                        ];
                    } else {
                        $data = [
                            'code' => 400,
                            'status' => 'error',
                            'message' => 'No existe el ayudante'
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
                'message' => 'Error al editar el ayudante',
                'request' => $request
            ];
        }
        return response() ->json($data);
    }

    public function delete(Request $request)
    {
        if ($request->rut == '') {
            $data = [
                'code' =>400,
                'status' => 'error',
                'mensaje' => 'Debe ingresar un RUT de un ayudante'
            ];
        } else {
            if ($this -> valida_rut($request ->rut)) {
                $ayudante = Ayudante::where('rut', $request ->rut)->first();
                if (empty($ayudante)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'mensaje' => 'No se encontro el ayudante asociado al rut'
                ];
                } else {
                    $ayudante -> delete();
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'mensaje' => 'Se ha eliminado correctamente'
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
