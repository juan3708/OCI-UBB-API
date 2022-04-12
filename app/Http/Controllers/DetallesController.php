<?php

namespace App\Http\Controllers;

use App\Models\Detalles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DetallesController extends Controller
{
    public function all()
    {
        $detalles = DB::table('detalles')->select('*')->get();
        $data = [
            'code' => 200,
            'detalles' => $detalles
        ];
        return response() ->json($data);
    }

    public function create(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'valor' => 'required',
                'descripcion' => 'required',
                'gastos_id' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Ingrese todos los datos porfavor',
                    'errors' => $validate ->errors()
                ];
            } else {
                $detalles = new Detalles();
                $detalles -> valor = $request -> valor;
                $detalles -> gastos_id = $request -> gastos_id;
                $detalles -> descripcion = $request -> descripcion;
                $detalles ->save();
                $data = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Se ha creado correctamente el detalle',
                            'detalles' => $detalles
                        ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al crear el detalle'
            ];
        }
        return response() ->json($data);
    }

    public function edit(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'valor' => 'required',
                'descripcion' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Ingrese todos los datos porfavor',
                    'errors' => $validate ->errors()
                ];
            } else {
                $detalles = Detalles::find($request->id);
                if (!empty($detalles)) {
                    $detalles -> valor = $request -> valor;
                    $detalles -> gastos_id = $request -> gastos_id;
                    $detalles -> descripcion = $request -> descripcion;
                    $detalles ->save();
                    $data = [
                                'code' => 200,
                                'status' => 'success',
                                'message' => 'Se ha editado correctamente el detalle',
                                'detalles' => $detalles
                            ];
                } else {
                    $data = [
                            'code' => 400,
                            'status' => 'error',
                            'message' => 'No existe un detalle asociadado'
                        ];
                }
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al editar el detalle'
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
                'mensaje' => 'Debe ingresar un detalle'
            ];
        } else {
            $detalles = Detalles::find($request->id);
            if (empty($detalles)) {
                $data = [
                    'code' =>400,
                    'status' => 'error',
                    'mensaje' => 'No se encontro un detalle asociada al ID'
                ];
            } else {
                $detalles ->delete();
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
