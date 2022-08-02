<?php

namespace App\Http\Controllers;

use App\Imports\StudentsImport;
use App\Models\Establecimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\Node\Stmt\TryCatch;

class EstablecimientoController extends Controller
{
    public function all()
    {
        //$establecimiento = DB::table('establecimiento')->select('*')->get();
        $establecimiento = Establecimiento::with('alumnos')->get();
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
                $establecimiento -> nombre =  strtoupper($request -> nombre);
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
                    $establecimiento -> nombre = strtoupper($request -> nombre);
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
            $establecimiento = Establecimiento::find($request->id);
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
                $establecimiento = Establecimiento::with('alumnos')->firstwhere('id', $request ->id);
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

    public function chargeStudentPerForm(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                    'file' =>'required|file',
                    'ciclo_id' => 'required'
                ]);
            if ($validate ->fails()) {
                $data = [
                        'code' => 400,
                        'status' => 'error',
                        'errors' => $validate ->errors(),
                        'file' => $request -> file,
                        'ciclo_id' => $request ->ciclo_id
                    ];
            } else {
                $file = $request -> file;
                try {
                    Excel::import(new StudentsImport($request -> ciclo_id), $file);

                } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                    $failures = $e->failures();
                    // foreach ($failures as $failure) {
                    //     var_dump("Rows", $failure->row());// row that went wrong
                    //     var_dump("Attribute", $failure->attribute()); // either heading key (if using heading row concern) or column index
                    //     var_dump("Errors", $failure->errors());// Actual error messages from Laravel validator
                    //     var_dump("Values Wrongs", $failure->values()); // The values of the row that has failed.
                    // }

                    $data = [
                        'code' =>400,
                        'status' => 'error',
                        'msg' => 'Porfavor revise el formato del archivo'
                    ];
                    return response() -> json($data);
                }
                $data = [
                    'code' =>200,
                    'status' => 'success',
                ];
            }
        } else {
            $data = [
                    'code' =>400,
                    'status' => 'error',
                    'msg' => 'Error en el request'
                ];
        }
        return response() -> json($data);
    }
}
