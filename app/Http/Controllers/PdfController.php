<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PdfController extends Controller
{
    public function AssistancePerEstablishment(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                    'students' => 'required',
                    'nombreCiclo' =>'required',
                    'nombreEstablecimiento' =>'required',
                    'email' =>'required',
                    'emailProfesor' =>'required'

                ]);
            if ($validate ->fails()) {
                $data = [
                        'code' => 400,
                        'status' => 'error',
                        'errors' => $validate ->errors()
                    ];
            } else {
                $fileName = 'asistencia-alumnos-'.$request->nombreEstablecimiento.'.pdf';
                $data = [
                    'nombreCiclo' => $request ->nombreCiclo,
                    'nombreEstablecimiento' => $request->nombreEstablecimiento,
                    'email' => $request->email,
                    'emailProfesor' => $request->emailProfesor,
                    'cantEstudiantes' => count($request->students),
                    'students' => $request->students,
                    
                ];
                $pdf = Pdf::loadView('pdfs.asistenciasPorEstablecimiento',$data )->setPaper('letter');
                Storage::put('temp\\'.$fileName, $pdf->output());
                $data1 = [
                        'code' =>200,
                        'status' => 'success',
                        'fileName' => $fileName
                    ];
            }
        } else {
            $data = [
                    'code' =>400,
                    'status' => 'error'
                ];
        }
        return response()-> json($data1);
    }

    public function download($fileName)
    {
        $path = storage_path('app\temp'."\\".$fileName);
        return response()->download($path);
    }


    public function delete(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                'fileName' => 'required'
            ]);
            if ($validate ->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validate ->errors()
                ];
            } else {
                $fileName = $request->fileName;
                $path = storage_path('app\temp'."\\".$fileName);
                File::delete($path);

                $data = [
                        'code' =>200,
                        'status' => 'success'
                    ];
            }
        } else {
            $data = [
                'code' =>400,
                'status' => 'error'
            ];
        }
        return response()-> json($data);
    }
}
