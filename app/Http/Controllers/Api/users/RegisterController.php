<?php

namespace App\Http\Controllers\Api\users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
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
