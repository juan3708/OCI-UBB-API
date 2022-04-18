<?php

namespace App\Http\Controllers;

use App\Models\Coordinador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CoordinadorController extends Controller
{
    //

    public function all()
    {
        $coordinador = DB::table('coordinador')->select('*')->get();
        $data = [
            'code' => 200,
            'coordinadores' => $coordinador
        ];
        return response() ->json($data);
    }

    public function create(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'nombre' => 'required',
                'apellidos' => 'required',
                'email' => 'required|email:rfc,dns',
                'rut' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                if ($this ->valida_rut($request ->rut)) {
                    $coordinador = Coordinador::where('rut', $request ->rut)->first();
                    if (empty($coordinador)) {
                        $coordinador = new Coordinador();
                        $coordinador -> nombre = $request -> nombre;
                        $coordinador -> apellidos = $request -> apellidos;
                        $coordinador -> email = $request -> email;
                        $coordinador -> rut = $request -> rut;
                        $coordinador ->save();
                        $data = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Se ha creado correctamente el coordinador',
                            'coordinador' => $coordinador
                        ];
                    } else {
                        $data = [
                            'code' => 400,
                            'status' => 'error',
                            'message' => 'Ya existe el coordinador'
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
                'message' => 'Error al crear el Coordinador'
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
                'id' =>'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                if ($this ->valida_rut($request ->rut)) {
                    $coordinador = Coordinador::find($request ->id);
                    if (!empty($coordinador)) {
                        $coordinador -> nombre = $request -> nombre;
                        $coordinador -> apellidos = $request -> apellidos;
                        $coordinador -> email = $request -> email;
                        $coordinador -> rut = $request -> rut;
                        $coordinador ->save();
                        $data = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Se ha editado correctamente el coordinador',
                            'coordinador' => $coordinador
                        ];
                    } else {
                        $data = [
                            'code' => 400,
                            'status' => 'error',
                            'message' => 'No existe el coordinador asociado a ese rut'
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
                'message' => 'Error al editar el Coordinador',
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
                'mensaje' => 'Debe ingresar un RUT de un Coordinador'
            ];
        } else {
            if ($this -> valida_rut($request ->rut)) {
                $coordinador = Coordinador::where('rut', $request ->rut)->first();
                if (empty($coordinador)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'mensaje' => 'No se encontro el coordinador asociado al rut'
                ];
                } else {
                    $coordinador -> delete();
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
