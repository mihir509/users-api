<?php

namespace App\Http\Controllers\Api\users;

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
}
