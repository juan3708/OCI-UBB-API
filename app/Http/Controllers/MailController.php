<?php

namespace App\Http\Controllers;

use App\Mail\InvitacionesOci;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    //

    public function invitations(Request $request){
        {
            if (!empty($request ->all())) {
                $validate = Validator::make($request ->all(), [
                    'subject' =>'required',
                    'emails' => 'required',
                    "content" => 'required|min:3',
                    "start_date" => 'required',
                    "formLink" => 'required'
                ]);
                if ($validate ->fails()) {
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                        'errors' => $validate ->errors()
                    ];
                } else {
                    Mail::to($request ->emails)->send(new InvitacionesOci($request -> subject, $request-> content,$request ->start_date, $request ->formLink));
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
}
