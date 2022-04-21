<?php

namespace App\Http\Controllers;

use App\Models\Establecimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EstablecimientoController extends Controller
{
    public function all()
    {
        //$establecimiento = DB::table('establecimiento')->select('*')->get();
        $establecimiento = Establecimiento::all();
        $data = [
            'code' => 200,
            'establecimientos' => $establecimiento
        ];
        return response() ->json($data);
    }
    public function create(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                    'nombre' => 'required',
                    'nombre_profesor' => 'required',
                    'email_profesor' => 'required|email:rfc,dns||unique:establecimiento,email_profesor',
                    'telefono_profesor' => 'required',
                    'direccion' => 'required',
                    'director' => 'required',
                ]);
            if ($validate ->fails()) {
                $data = [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'Ingrese todos los datos porfavor',
                        'errors' => $validate ->errors()
                    ];
            } else {
                $establecimiento = new establecimiento();
                $establecimiento -> nombre = $request -> nombre;
                $establecimiento -> telefono = $request -> telefono;
                $establecimiento -> email = $request -> email;
                $establecimiento -> nombre_profesor = $request -> nombre_profesor;
                $establecimiento -> email_profesor = $request -> email_profesor;
                $establecimiento -> telefono_profesor = $request -> telefono_profesor;
                $establecimiento -> direccion = $request -> direccion;
                $establecimiento -> director = $request -> director;
                $establecimiento ->save();
                $data = [
                                'code' => 200,
                                'status' => 'success',
                                'message' => 'Se ha creado correctamente el establecimiento',
                                'establecimiento' => $establecimiento
                            ];
            }
        } else {
            $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Error al crear el establecimiento'
                ];
        }
        return response() ->json($data);
    }

    public function edit(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                    'nombre' => 'required',
                    'nombre_profesor' => 'required',
                    'email_profesor' => 'required|email:rfc,dns',
                    'telefono_profesor' => 'required',
                    'direccion' => 'required',
                    'director' => 'required',
                ]);
            if ($validate ->fails()) {
                $data = [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'Ingrese todos los datos porfavor',
                        'errors' => $validate ->errors()
                    ];
            } else {
                $establecimiento = Establecimiento::find($request->id);
                if (!empty($establecimiento)) {
                    $establecimiento -> nombre = $request -> nombre;
                    $establecimiento -> telefono = $request -> telefono;
                    $establecimiento -> email = $request -> email;
                    $establecimiento -> nombre_profesor = $request -> nombre_profesor;
                    $establecimiento -> email_profesor = $request -> email_profesor;
                    $establecimiento -> telefono_profesor = $request -> telefono_profesor;
                    $establecimiento -> direccion = $request -> direccion;
                    $establecimiento -> director = $request -> director;
                    $establecimiento ->save();
                    $data = [
                                    'code' => 200,
                                    'status' => 'success',
                                    'message' => 'Se ha editado correctamente el establecimiento',
                                    'establecimiento' => $establecimiento
                                ];
                } else {
                    $data = [
                                'code' => 401,
                                'status' => 'error',
                                'message' => 'No existe un establecimiento asociado'
                            ];
                }
            }
        } else {
            $data = [
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Error al editar el establecimiento'
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
                    'message' => 'Debe ingresar un establecimiento'
                ];
        } else {
            $establecimiento = establecimiento::find($request->id);
            if (empty($establecimiento)) {
                $data = [
                        'code' =>400,
                        'status' => 'error',
                        'message' => 'No se encontro el establecimiento'
                    ];
            } else {
                $establecimiento ->delete();
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
                $establecimiento = Establecimiento::find($request ->id);
                if (empty($establecimiento)) {
                    $data = [
                        'code' =>400,
                        'status' => 'error',
                        'message' => 'No se encontro el establecimiento asociado al id'
                    ];
                } else {
                    $data = [
                        'code' =>200,
                        'status' => 'success',
                        'establecimiento' => $establecimiento
                    ];
                }
            }
        } else {
            $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'Error al buscar el establecimiento'
                ];
        }
        return response() -> json($data);
    }
}
