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
                $dataView = [
                    'nombreCiclo' => $request ->nombreCiclo,
                    'nombreEstablecimiento' => $request->nombreEstablecimiento,
                    'email' => $request->email,
                    'emailProfesor' => $request->emailProfesor,
                    'cantEstudiantes' => count($request->students),
                    'students' => $request->students,
                    
                ];
                $pdf = Pdf::loadView('pdfs.asistenciasPorEstablecimiento',$dataView )->setPaper('letter');
                Storage::put('temp\\'.$fileName, $pdf->output());
                $data = [
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
        return response()-> json($data);
    }

    public function GeneralAssistance(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                    'establecimientos' => 'required',
                    'establecimientoConMenosEstudiantes' =>'required',
                    'establecimientoConMasEstudiantes' =>'required',
                    'totalAlumnos' =>'required',
                    'totalEstablecimientos' =>'required',
                    'nombreCiclo'=>'required'

                ]);
            if ($validate ->fails()) {
                $data = [
                        'code' => 400,
                        'status' => 'error',
                        'errors' => $validate ->errors()
                    ];
            } else {
                $fileName = 'asistencia-general-'.$request->nombreCiclo.'.pdf';
                $dataView = [
                    'establecimientos' => $request ->establecimientos,
                    'establecimientoConMenosEstudiantes' => $request->establecimientoConMenosEstudiantes,
                    'establecimientoConMasEstudiantes' => $request->establecimientoConMasEstudiantes,
                    'totalAlumnos' => $request->totalAlumnos,
                    'totalEstablecimientos' => $request->totalEstablecimientos,
                    'nombreCiclo' => $request->nombreCiclo
                    
                ];
                $pdf = Pdf::loadView('pdfs.asistenciaGeneral',$dataView )->setPaper('letter');
                Storage::put('temp\\'.$fileName, $pdf->output());
                $data = [
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
        return response()-> json($data);
    }

    public function Costs(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                    'nombreCiclo' => 'required',
                    'fecha_inicio' =>'required',
                    'fecha_final' =>'required',
                    'gastos' =>'required',
                    'totalGastado' =>'required',
                    'presupuestoCiclo'=>'required'

                ]);
            if ($validate ->fails()) {
                $data = [
                        'code' => 400,
                        'status' => 'error',
                        'errors' => $validate ->errors()
                    ];
            } else {
                $fileName = 'asistencia-general-'.$request->nombreCiclo.'.pdf';
                $parseFechaInicio = date("d/m/Y", strtotime($request->fecha_inicio));
                $parseFechaFinal = date("d/m/Y", strtotime($request->parseFechaFinal));

                $dataView = [
                    'nombreCiclo' => $request ->nombreCiclo,
                    'fecha_inicio' => $parseFechaInicio,
                    'fecha_final' => $parseFechaFinal,
                    'gastos' => $request->gastos,
                    'totalGastado' => $request->totalGastado,
                    'presupuestoCiclo' => $request->presupuestoCiclo
                    
                ];
                $pdf = Pdf::loadView('pdfs.asistenciaGeneral',$dataView )->setPaper('letter');
                Storage::put('temp\\'.$fileName, $pdf->output());
                $data = [
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
        return response()-> json($data);
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
