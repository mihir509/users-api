<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;



class LoginController extends Controller
{
    public function login(Request $request)
    {

        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
    
            $user = User::where('email', $request->email)->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }
    
            $token = $user->createToken('authToken')->plainTextToken;
    
            $response = [
                'data' => $user,
                'token' => $token,
                'message' => 'User login successfully',
                'success' => true,
            ];

            return response()->json($response, 200);
            
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Login failed',
                'success' => false,
            ], 401);
        }
                    
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);

            $user = User::create([  
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $response = [
                'data' => $user,
                'message' => 'User registered successfully',
                'success' => true,
            ];
            return response()->json($response, 201);

        } catch (\Throwable $th) {
    
            $errors = $th->errors();
            $response = [
                'data' => null,
                'message' => $errors,
                'success' => false,
            ];
           return response()->json($response , 422);
        }
        
    }
}
