<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'email' => 'required|email:rfc,dns||unique:user,email',
            'password' => 'required|string|min:6',
            'fecha_creacion' => 'required|date_format:Y-m-d',
            'rut' => 'required',
            'rol_id' => 'required'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),400);
        }
        $user = User::create(array_merge(
            $validator->validate(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => '¡Usuario registrado exitosamente!',
            'user' => $user
        ], 201);
    }

    public function login()
    {
        $credentials = request(['rut', 'password']);
        $user = User::where('rut', $credentials['rut'])->first();
        if(isset($user)){
            if(Hash::check($credentials['password'], $user->password)){
                if ($token = auth()->claims(['user'=> $user->load('rol')]) -> login($user)) {
                    return $this->respondWithToken($token);
                }else{
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
            }else{
                $data = [
                    'code' =>400,
                    'status' => 'error',
                    'message' => 'Contraseña incorrecta'
                ];
                return response()->json($data);
            }
        }else{
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
    
}
