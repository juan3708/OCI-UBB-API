<?php

namespace App\Http\Controllers;

use App\Models\Nivel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class NivelController extends Controller
{
    public function all()
    {
    
        $nivel = Nivel::with('clases','ciclo')->get();
        $data = [
            'code' => 200,
            'niveles' => $nivel
        ];
        return response() ->json($data);
    }

    public function create(Request $request)
    {
        if (!empty($request ->all())) {
            
            $validate = Validator::make($request ->all(), [
                'nombre' => 'required',
                'ciclo_id' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $nivel = Nivel::where('nombre', $request ->nombre)->first();
                if (empty($nivel)) {
                    $nivel = new Nivel();
                    $nivel -> nombre = $request -> nombre;
                    $nivel -> ciclo_id = $request -> ciclo_id;
                    $nivel ->save();
                    $data = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Se ha creado correctamente el nivel',
                            'nivel' => $nivel
                        ];
                } else {
                    $data = [
                            'code' => 401,
                            'status' => 'error',
                            'message' => 'Ya existe un nivel con ese nombre'
                        ];
                }
            }
        } else {
            $data = [
                'code' => 401,
                'status' => 'error',
                'message' => 'Error al crear el nivel'
            ];
        }
        return response() ->json($data);
    }

    public function edit(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'id' => 'required',
                'ciclo_id' => 'required',
                'nombre' => 'required',
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $nivel = Nivel::find($request->id);
                if (!empty($nivel)) {
                    $nivel -> nombre = $request -> nombre;
                    $nivel -> ciclo_id = $request -> ciclo_id;
                    $nivel ->save();
                    $data = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Se ha editado correctamente el nivel',
                            'nivel' => $nivel
                        ];
                } else {
                    $data = [
                            'code' => 401,
                            'status' => 'error',
                            'message' => 'No existe un nivel asociado'
                        ];
                }
            }
        } else {
            $data = [
                'code' => 401,
                'status' => 'error',
                'message' => 'Error al editar el nivel'
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
                'message' => 'Debe ingresar un nivel'
            ];
        } else {
            $nivel = Nivel::find($request->id);
            if (empty($nivel)) {
                $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro el nivel'
                ];
            } else {
                $nivel ->delete();
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
                $nivel = Nivel::with('clases','ciclo','alumnos')->firstwhere('id',$request ->id);
                if (empty($nivel)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro el nivel asociado al id'
                ];
                } else {
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'nivel' => $nivel
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al buscar el nivel'
            ];
        }
        return response() -> json($data);
    }
    
    public function alumnoHasLevel( Request $request){
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'nivel_id' =>'required',
                'alumnos_id' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $nivel = Nivel::find($request ->nivel_id);
                if (empty($nivel)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro el nivel'
                ];
                } else {
                    $nivel -> alumnos()->attach($request -> alumnos_id);
                    $nivel = Nivel::with('alumnos')->firstwhere('id',$request->nivel_id);
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'nivel' => $nivel
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al asociar nivel con clase'
            ];
        }
        return response()-> json($data);
    }
}
