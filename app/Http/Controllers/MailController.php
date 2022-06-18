<?php

namespace App\Http\Controllers;

use App\Mail\ContactanosOci;
use App\Mail\ContactanosOciResp;
use App\Mail\InvitacionesOci;
use App\Mail\MensajeEstablecimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    //
    public $contactEmail = 'matias.elgueta.duarte@gmail.com';

    public function invitations(Request $request)
    {
        {
            if (!empty($request ->all())) {
                $validate = Validator::make($request ->all(), [
                    'subject' =>'required',
                    'emails' => 'required',
                    "content" => 'required|min:3',
                    "start_date" => 'required'
                ]);
                if ($validate ->fails()) {
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                        'errors' => $validate ->errors()
                    ];
                } else {
                    Mail::to($request ->emails)->send(new InvitacionesOci($request -> subject, $request-> content, $request ->start_date, $request ->formLink));
                    $data = [
                        'code' =>200,
                        'status' => 'success',
                    ];
                }
            } else {
                $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'Error al enviar las invitaciones'
                ];
            }
            return response()-> json($data);
        }
    }

    public function messages(Request $request)
    {
        {
            if (!empty($request ->all())) {
                $validate = Validator::make($request ->all(), [
                    'subject' =>'required',
                    'emails' => 'required',
                    "content" => 'required|min:3',
                    "cycleName" => 'required'
                ]);
                if ($validate ->fails()) {
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                        'errors' => $validate ->errors()
                    ];
                } else {
                    Mail::to($request ->emails)->send(new MensajeEstablecimiento($request ->cycleName,$request -> subject, $request-> content ));
                    $data = [
                        'code' =>200,
                        'status' => 'success',
                    ];
                }
            } else {
                $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'Error al enviar las invitaciones'
                ];
            }
            return response()-> json($data);
        }
    }

    public function contact(Request $request)
    {
        {
            if (!empty($request ->all())) {
                $validate = Validator::make($request ->all(), [
                    'name' =>'required',
                    'email' => 'required',
                    "subject" => 'required|min:3',
                    "content" => 'required'
                ]);
                if ($validate ->fails()) {
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                        'errors' => $validate ->errors()
                    ];
                } else {
                    Mail::to($this->contactEmail)->send(new ContactanosOci($request -> subject, $request ->name, $request-> content, $request ->email));
                    Mail::to($request->email)->send(new ContactanosOciResp($request->name));
                    $data = [
                        'code' =>200,
                        'status' => 'success',
                    ];
                }
            } else {
                $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'Error al enviar el correo'
                ];
            }
            return response()-> json($data);
        }
    }

    public function changePassword(Request $request)
    {
        {
            if (!empty($request ->all())) {
                $validate = Validator::make($request ->all(), [
                    'name' =>'required',
                    'email' => 'required',
                    "subject" => 'required|min:3',
                    "content" => 'required'
                ]);
                if ($validate ->fails()) {
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                        'errors' => $validate ->errors()
                    ];
                } else {
                    Mail::to($this->contactEmail)->cc($request ->email)->send(new ContactanosOci($request -> subject, $request ->name, $request-> content, $request ->email));
                    Mail::to($request->email)->send(new ContactanosOciResp($request->name));
                    $data = [
                        'code' =>200,
                        'status' => 'success',
                    ];
                }
            } else {
                $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'Error al enviar el correo'
                ];
            }
            return response()-> json($data);
        }
    }
}
