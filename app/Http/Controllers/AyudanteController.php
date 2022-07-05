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
        /* $ayudante = DB::table('ayudante')->select('*')->get();*/
        $ayudante = Ayudante::all();
        $data = [
            'code' => 200,
            'ayudantes' => $ayudante
        ];
        return response() ->json($data);
    }

    public function create(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'nombre' => 'required',
                'apellidos' => 'required',
                'email' => 'required|email:rfc,dns|unique:ayudante,email',
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
                            'code' => 401,
                            'status' => 'error',
                            'message' => 'Ya existe el ayudante'
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
                'email' => 'required|email:rfc,dns',
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
                            'code' => 401,
                            'status' => 'error',
                            'message' => 'No existe el ayudante'
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
                'message' => 'Error al editar el ayudante',
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
                'message' => 'Debe ingresar un ayudante'
            ];
        } else {
            $ayudante = Ayudante::find($request->id);
            if (empty($ayudante)) {
                $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro el ayudante'
                ];
            } else {
                $ayudante = Ayudante::with('clases')->firstwhere('id', $request ->id);

                //Detach clases
                $array_id = array();
                foreach ($ayudante->clases as $key => $nivel) {
                    $array_id[] = $ayudante->clases[$key]->id;
                };
                $ayudante -> clases()-> detach($array_id);
                $ayudante -> delete();
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
                $ayudante = Ayudante::find($request ->id);
                if (empty($ayudante)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro el ayudante asociado al id'
                ];
                } else {
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'ayudante' => $ayudante
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al buscar el ayudante'
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
