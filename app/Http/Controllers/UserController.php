<?php

namespace App\Http\Controllers;

use App\Mail\Bienvenidos;
use App\Mail\CambiarContraseña;
use App\Mail\CambiarCorreo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['all', 'login', 'register','changeStatus','ResetPassword','ChangePassword','Edit','ChangeEmail']]);
    }

    public function all()
    {
        $user = User::with('rol')->get();
        $data = [
            'code' => 200,
            'usuarios' => $user
        ];
        return response() ->json($data);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'apellidos' => 'required',
            'email' => 'required|email:rfc,dns||unique:user,email',
            'password' => 'required|string|min:6',
            'fecha_creacion' => 'required|date_format:Y-m-d',
            'rut' => 'required',
            'rol_id' => 'required',
            'admin'=>'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->toJson(),
                'code' => 400
            ]);
        }
        $user = User::create(array_merge(
            $validator->validate(),
            ['password' => bcrypt($request->password)],
            ['activo' => 1],
            ['admin?'=>0]
        ));
        $newDate = date("d/m/Y", strtotime($request->fecha_creacion));
        Mail::to($request->email)->send(new Bienvenidos($request->nombre." ".$request->apellidos, $newDate));
        return response()->json([
            'message' => '¡Usuario registrado exitosamente!',
            'user' => $user,
            'code' => 200
        ]);
    }

    public function Edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'apellidos' => 'required',
            'rut' => 'required',
            'email' => 'required',
            'rol_id' => 'required',
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->toJson(),
                'code' => 400
            ]);
        } else {
            $user = User::find($request->id);
            if (empty($user)) {
                $data = [
                        'code' => 400,
                        'status' => 'error',
                        'msg' => 'error en el find'
                            ];
            } else {
                $user ->nombre = $request->nombre;
                $user ->apellidos = $request->apellidos;
                $user ->rut = $request->rut;
                $user ->email = $request->email;
                $user ->rol_id = $request->rol_id;
                $user->save();
                $data = [
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'Se ha editado correctamente el usuario',
                        'usuario' => $user
                            ];
            }
        }

        return response() ->json($data);
    }

    public function login()
    {
        $credentials = request(['rut', 'password']);
        $user = User::where('rut', $credentials['rut'])->first();
        if (isset($user)) {
            if (Hash::check($credentials['password'], $user->password)) {
                if ($user->activo == 1) {
                    if ($token = auth()->claims(['user'=> $user->load('rol')]) -> login($user)) {
                        return $this->respondWithToken($token);
                    } else {
                        return response()->json(['error' => 'Unauthorized'], 401);
                    }
                } else {
                    $data = [
                        'code' =>402,
                        'status' => 'error',
                        'message' => 'El usuario no está activo, por favor comuníquese con el administrador'
                    ];
                    return response()->json($data);
                }
            } else {
                $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'Contraseña incorrecta'
                ];
                return response()->json($data);
            }
        } else {
            $data = [
                'code' =>402,
                'status' => 'error',
                'message' => 'No existe un usuario asociado a este rut'
            ];
            return response()->json($data);
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'code' =>200,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function changeStatus(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                    'user_id' => 'required',
                    'activo' => 'required'
                ]);
            if ($validate ->fails()) {
                $data = [
                        'code' => 400,
                        'status' => 'error',
                    ];
            } else {
                $user = User::find($request->user_id);
                if ($user->admin != 1) {
                    $user -> activo = $request->activo;
                    $user->save();
                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'Se ha cambiado correctamente el estado',
                            ];
                } else {
                    $data = [
                        'code' => 401,
                        'status' => 'error',
                        'message' => 'No se le puede cambiar el estado al usuario administrador',
                            ];
                }
            }
        } else {
            $data = [
                    'code' => 400,
                    'status' => 'error'
                ];
        }
        return response() ->json($data);
    }

    public function ResetPassword(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                    'rut' => 'required',
                    'email' => 'required',
                    'fecha' =>'required'
                ]);
            if ($validate ->fails()) {
                $data = [
                        'code' => 400,
                        'status' => 'error',
                    ];
            } else {
                $user = User::where('rut', $request->rut)->where('email', $request->email)->get();
                if ($user->isEmpty()) {
                    $data = [
                        'code' => 401,
                        'status' => 'error',
                        'message' => 'No se encuentra usuario asociado a ese Rut y Correo.',
                            ];
                } else {
                    $newDate = date("d/m/Y", strtotime($request->fecha));
                    $passwordWithOutEncrytp = Str::random(8);
                    $user[0] ->password = bcrypt($passwordWithOutEncrytp);
                    $user[0]-> save();
                    Mail::to($request->email)->send(new CambiarContraseña($user[0]->nombre." ".$user[0]->apellidos, $passwordWithOutEncrytp, $newDate));
                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'Se ha enviado la contraseña generada al correo',
                            ];
                }
            }
        } else {
            $data = [
                    'code' => 400,
                    'status' => 'error'
                ];
        }
        return response() ->json($data);
    }

    public function ChangePassword(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                    'passwordActual' => 'required',
                    'newPassword' => 'required|string|min:6',
                    'user_id' =>'required'
                ]);
            if ($validate ->fails()) {
                $data = [
                        'code' => 401,
                        'status' => 'error',
                        'message'=> 'Las contraseñas deben tener minimo 6 caracteres'
                    ];
            } else {
                $user = User::find($request->user_id);
                if (empty($user)) {
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                            ];
                } else {
                    if (Hash::check($request->passwordActual, $user->password)) {
                        $user ->password = bcrypt($request->newPassword);
                        $user-> save();
                        $data = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Se ha cambiado correctamente la contraseña',
                                ];
                    } else {
                        $data = [
                            'code' => 401,
                            'status' => 'error',
                            'message' => 'La contraseña actual es incorrecta',
                                ];
                    }
                }
            }
        } else {
            $data = [
                    'code' => 400,
                    'status' => 'error'
                ];
        }
        return response() ->json($data);
    }

    public function ChangeEmail(Request $request)
    {
        if (!empty($request ->all())) {
            $validate = Validator::make($request ->all(), [
                    'password' => 'required',
                    'newEmail' => 'required|email:rfc,dns||unique:user,email',
                    'user_id' =>'required'
                ]);
            if ($validate ->fails()) {
                $data = [
                        'code' => 401,
                        'status' => 'error',
                        'message' => $validate->errors()->toJson(),
                    ];
            } else {
                $user = User::find($request->user_id);
                if (empty($user)) {
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                            ];
                } else {
                    if (Hash::check($request->password, $user->password)) {
                        $newDate = date("d/m/Y");
                        $email = $user->email;
                        $user ->email = $request->newEmail;
                        $user-> save();
                        Mail::to($email)->send(new CambiarCorreo($user->nombre." ".$user->apellidos, $request->newEmail, $newDate));
                        $data = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Se ha cambiado correctamente el correo',
                                ];
                    } else {
                        $data = [
                            'code' => 401,
                            'status' => 'error',
                            'message' => 'La contraseña es incorrecta',
                                ];
                    }
                }
            }
        } else {
            $data = [
                    'code' => 400,
                    'status' => 'error'
                ];
        }
        return response() ->json($data);
    }
}
