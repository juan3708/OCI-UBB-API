<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NoticiaController extends Controller
{
    public function all()
    {
        $noticia = DB::table('noticia')->select('*')->get();
        $data = [
            'code' => 200,
            'noticia' => $noticia
        ];
        return response() ->json($data);
    }

    public function create(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'fecha' => 'required',
                'titulo' => 'required',
                'cuerpo' => 'required',
                'ciclo_id' => 'required',
                'user_rut' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Ingrese todos los datos porfavor',
                    'errors' => $validate ->errors()
                ];
            } else {
                $noticia = new Noticia();
                $noticia -> fecha = $request -> fecha;
                $noticia -> cuerpo = $request -> cuerpo;
                $noticia -> titulo = $request -> titulo;
                $noticia -> ciclo_id = $request -> ciclo_id;
                $noticia -> user_rut = $request -> user_rut;
                $noticia ->save();
                $data = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Se ha creado correctamente la noticia',
                            'noticia' => $noticia
                        ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al crear la noticia'
            ];
        }
        return response() ->json($data);
    }

    public function edit(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'fecha' => 'required',
                'titulo' => 'required',
                'cuerpo' => 'required',
                'ciclo_id' => 'required',
                'user_rut' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Ingrese todos los datos porfavor',
                    'errors' => $validate ->errors()
                ];
            } else {
                $noticia = Noticia::find($request->id);
                if (!empty($noticia)) {
                    $noticia -> fecha = $request -> fecha;
                    $noticia -> cuerpo = $request -> cuerpo;
                    $noticia -> titulo = $request -> titulo;
                    $noticia -> ciclo_id = $request -> ciclo_id;
                    $noticia -> user_rut = $request -> user_rut;
                    $noticia ->save();
                    $data = [
                                'code' => 200,
                                'status' => 'success',
                                'message' => 'Se ha editado correctamente la noticia',
                                'noticia' => $noticia
                            ];
                } else {
                    $data = [
                            'code' => 400,
                            'status' => 'error',
                            'message' => 'No existe una noticia asociada'
                        ];
                }
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al editar la noticia'
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
                'mensaje' => 'Debe ingresar un ID de una noticia'
            ];
        } else {
            $noticia = Noticia::find($request->id);
            if (empty($noticia)) {
                $data = [
                    'code' =>400,
                    'status' => 'error',
                    'mensaje' => 'No se encontro una noticia asociada al ID'
                ];
            } else {
                $noticia ->delete();
                $data = [
                    'code' =>200,
                    'status' => 'success',
                    'mensaje' => 'Se ha eliminado correctamente'
                ];
            }
        }
        return response() -> json($data);
    }
}
