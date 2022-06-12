<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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

    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);
        //VALIDAR QUE SEA EL MISMO CORREO Y MISMA CONTRASEÑA
        $user = User::where('email', $request -> email)->first();
        //VALIDAR QUE EXISTA EL USUARIO, SI EXISTE IF (45), SINO MANDAR MJS DE ERROR DE CREDENCIALES INCORRECTAS
        if (! $token = auth()->claims(['user'=> $user]) -> attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
    
}
