<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    const ROLE_USER = 1;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),  // se puede hacer también con $request->input('name')
            'email' => $request->get('email'),
            'password' => bcrypt($request->password)  // aquí tb se podría usar get o input
        ]);

        $user->roles()->attach(self::ROLE_USER);  // añade el role con id=1, es decir, 'user'

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);
    }

    public function login(Request $request)
    {
        $input = $request->only('email', 'password');  // recupera solo email y password y los guarda en un array
        $jwt_token = null;

        if (!$jwt_token = JWTAuth::attempt($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], Response::HTTP_UNAUTHORIZED);  // es como poner un status 401 (no autorizado)
        }

        return response()->json([
            'success' => true,
            'token' => $jwt_token,
        ]);
    }

    public function myProfile()
    {
        return response()->json(auth()->user());  // saco la información del token
    }

    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        try {
            JWTAuth::invalidate($request->token);
            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);  // es como poner un status 500
        }
    }
}
