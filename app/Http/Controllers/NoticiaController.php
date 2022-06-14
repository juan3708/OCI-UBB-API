<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoticiaController extends Controller
{
    public function all()
    {
        /*$noticia = DB::table('noticia as n')->select('n.cuerpo',DB::raw('DATE_FORMAT(n.fecha, "%d-%m-%Y") as fecha')
        ,'n.titulo','n.user_rut','n.ciclo_id','n.id')->get();*/
        //$noticia = Noticia::with('ciclo','user')->all();
        $noticia = Noticia::with('adjuntos')->get();
        $data = [
            'code' => 200,
            'noticias' => $noticia
        ];
        return response() ->json($data);
    }

    public function create(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'fecha' => 'required|date_format:Y-m-d',
                'titulo' => 'required',
                'entrada' => 'required',
                'cuerpo' => 'required',
                'ciclo_id' => 'required',
                // 'user_rut' => 'required'
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
                $noticia -> entrada = $request -> entrada;
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
                'code' => 401,
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
                'fecha' => 'required|date_format:Y-m-d',
                'titulo' => 'required',
                'entrada' => 'required',
                'cuerpo' => 'required',
                'ciclo_id' => 'required',
                // 'user_rut' => 'required'
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
                    $noticia -> entrada = $request -> entrada;
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
                            'code' => 401,
                            'status' => 'error',
                            'message' => 'No existe una noticia asociada'
                        ];
                }
            }
        } else {
            $data = [
                'code' => 401,
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
                'message' => 'Debe ingresar un ID de una noticia'
            ];
        } else {
            $noticia = Noticia::find($request->id);
            if (empty($noticia)) {
                $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro una noticia asociada al ID'
                ];
            } else {
                $noticia ->delete();
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
                $noticia = Noticia::find($request ->id);
                if (empty($noticia)) {
                    $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'No se encontro la noticia asociado al id'
                ];
                } else {
                    $noticia = Noticia::with('adjuntos')->where($request->id)->get();
                    $adjuntos = $noticia ->adjuntos();
                    $data = [
                    'code' =>200,
                    'status' => 'success',
                    'noticia' => $noticia,
                    'adjuntos' => $adjuntos
                ];
                }
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Error al buscar la noticia'
            ];
        }
        return response() -> json($data);
    }
}
