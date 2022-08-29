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
                date_default_timezone_set('America/Santiago'); 
                $dataView = [
                    'nombreCiclo' => $request ->nombreCiclo,
                    'nombreEstablecimiento' => $request->nombreEstablecimiento,
                    'email' => $request->email,
                    'emailProfesor' => $request->emailProfesor,
                    'cantEstudiantes' => count($request->students),
                    'students' => $request->students,
                    'fechaEmision'=> date('d-m-Y h:i:s a', time())
                    
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
                date_default_timezone_set('America/Santiago'); 
                $fileName = 'asistencia-general-'.$request->nombreCiclo.'.pdf';
                $dataView = [
                    'establecimientos' => $request ->establecimientos,
                    'establecimientoConMenosEstudiantes' => $request->establecimientoConMenosEstudiantes,
                    'establecimientoConMasEstudiantes' => $request->establecimientoConMasEstudiantes,
                    'totalAlumnos' => $request->totalAlumnos,
                    'totalEstablecimientos' => $request->totalEstablecimientos,
                    'fechaEmision'=> date('d-m-Y h:i:s a', time()),
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
                date_default_timezone_set('America/Santiago'); 
                $fileName = 'informe-gastos-'.$request->nombreCiclo.'.pdf';
                $parseFechaInicio = date("d/m/Y", strtotime($request->fecha_inicio));
                $parseFechaFinal = date("d/m/Y", strtotime($request->fecha_final));

                $dataView = [
                    'nombreCiclo' => $request ->nombreCiclo,
                    'fecha_inicio' => $parseFechaInicio,
                    'fecha_final' => $parseFechaFinal,
                    'gastos' => $request->gastos,
                    'totalGastado' => $request->totalGastado,
                    'fechaEmision'=> date('d-m-Y h:i:s a', time()),
                    'presupuestoCiclo' => $request->presupuestoCiclo
                    
                ];
                $pdf = Pdf::loadView('pdfs.gastos',$dataView )->setPaper('letter');
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

    public function GeneralStatistic(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                    'cantEstablecimientos' => 'required',
                    'cantidadAlumnosInscritos' =>'required',
                    'cantidadAlumnosParticipantes' =>'required',
                    'cicloAnterior' =>'required',
                    'diferenciaAlumnosInscritos'=>'required',
                    'diferenciaAlumnosParticipantes' => 'required',
                    'diferenciaEstablecimientos' =>'required',
                    'establecimientoMaxInscritos' =>'required',
                    'establecimientoMaxParticipantes' =>'required',
                    'establecimientoMinInscritos' =>'required',
                    'establecimientoMinParticipantes'=>'required',
                    'establecimientos' => 'required',
                    'totalGastos' =>'required',
                    'prespuestoRestante' =>'required',
                    'ciclo' => 'required'

                ]);
            if ($validate ->fails()) {
                $data = [
                        'code' => 400,
                        'status' => 'error',
                        'errors' => $validate ->errors()
                    ];
            } else {
               // dd($request->ciclo['nombre']);
                $fileName = 'informe-general-'.$request->ciclo['nombre'].'.pdf';
                date_default_timezone_set('America/Santiago'); 

                $dataView = [
                    'cantEstablecimientos' => $request ->cantEstablecimientos,
                    'cantidadAlumnosInscritos' => $request->cantidadAlumnosInscritos,
                    'cantidadAlumnosParticipantes' => $request->cantidadAlumnosParticipantes,
                    'cicloAnterior' => $request->cicloAnterior,
                    'competencias' => $request->competencias,
                    'diferenciaAlumnosInscritos' => $request->diferenciaAlumnosInscritos,
                    'diferenciaAlumnosParticipantes' => $request ->diferenciaAlumnosParticipantes,
                    'diferenciaEstablecimientos' => $request->diferenciaEstablecimientos,
                    'establecimientoMaxInscritos' => $request->establecimientoMaxInscritos,
                    'establecimientoMaxParticipantes' => $request->establecimientoMaxParticipantes,
                    'establecimientoMinInscritos' => $request->establecimientoMinInscritos,
                    'establecimientoMinParticipantes' => $request->establecimientoMinParticipantes,
                    'establecimientos' => $request ->establecimientos,
                    'gastos' => $request->gastos,
                    'totalGastos' => $request->totalGastos,
                    'fechaEmision'=> date('d-m-Y h:i:s a', time()),
                    'prespuestoRestante' => $request->prespuestoRestante,
                    'ciclo' => $request ->ciclo
                    
                ];
                $pdf = Pdf::loadView('pdfs.estadisticaGeneral',$dataView )->setPaper('letter');
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
