<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Adjuntos;
use App\Models\Noticia;
use Illuminate\Support\Facades\File;

class AdjuntosController extends Controller
{
    public function add(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'noticia_id' => 'required',
                'image' => 'required|image'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Ingrese todos los datos porfavor',
                    'errors' => $validate ->errors()
                ];
            } else {
                $noticia = Noticia::find($request->noticia_id);
                if (empty($noticia)) {
                    $data = [
                        'code' =>400,
                        'status' => 'error',
                        'message'=>'error al encontrar noticia'
                    ];
                } else {
                    $adjunto = New Adjuntos();
                    if ($request->hasFile('image')) {
                        $completeFileName = $request->file('image')->getClientOriginalName();
                        $fileNameOnly=pathinfo($completeFileName, PATHINFO_FILENAME);
                        $extension = $request->file('image')->getClientOriginalExtension();
                        $compPic = str_replace(' ','_',$fileNameOnly).'-'.rand().'_'.time().'.'.$extension;
                        $path = $request->file('image')->storeAs('public/images',$compPic);
                    }
                    $adjunto->url = $compPic;
                    $adjunto -> noticia_id = $request ->noticia_id;
                    $adjunto->save();
                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'adjunto'=>$adjunto
                    ]; 
                }
            }
        } else {
            $data = [
                'code' => 401,
                'status' => 'error'
            ];
        }
        return response() ->json($data);
    }

// Eliminar

    public function delete(Request $request)
    {
        if ($request->id == '') {
            $data = [
                'code' =>400,
                'status' => 'error',
                'message' => 'Debe ingresar un ID de una noticia'
            ];
        } else {
            $adjunto = Adjuntos::find($request->id);
            if (empty($adjunto)) {
                $data = [
                    'code' =>400,
                    'status' => 'error',
                ];
            } else {
                if(File::exists('storage/images/'.$adjunto->url)){
                    File::delete('storage/images/'.$adjunto->url);
                    $adjunto ->delete();
                }

                $data = [
                    'code' =>200,
                    'status' => 'success',
                ];
            }
        }
        return response() -> json($data);
    }
}
