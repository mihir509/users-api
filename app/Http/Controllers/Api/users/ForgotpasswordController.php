<?php

namespace App\Http\Controllers\Api\users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordEmail;

class ForgotpasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {

        try {
            $request->validate([
                'email' => 'required|email',
            ]);
    
            $user = User::where('email', $request->email)->first();
            
            
            if (!$user) {

                $response = [
                    'data' => null,
                    'message' => 'We could not find a user with that email address',
                    'success' => false,
                ];
                return response()->json($response , 400);

            }

            $token = Str::random(60);

            DB::table('password_reset_tokens')->insert([
                'email' => $user->email,
                'token' => $token,
                'created_at' => now(),
            ]);
    
            $resetLink = url('/forgot-password?token='.$token);

            Mail::to($user->email)->send(new ResetPasswordEmail($resetLink));
    
            $response = [
                'data' =>  $resetLink,
                'message' => 'Password reset link sent to your email.',
                'success' => true,
            ];

            return response()->json($response , 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while processing the request.',
            ], 500);
        }

    }

    public function verifyToken(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string',
            ]);
    
            $token = $request->input('token');
    
            $passwordReset = DB::table('password_reset_tokens')
                ->where('token', $token)
                ->first();
    
            if (!$passwordReset) {
                return response()->json([
                    'data' => null,
                    'message' => 'Token is invalid',
                    'success' => false
                ], 400);
            }
    
            return response()->json([
                'data' => null,
                'message' => 'Token is valid.',
                'success' => true
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'data' => null,
                'message' => 'Something went wrong.',
                'success' => false
            ], 400);
        }
    }

    public function resetPassword(Request $request)
    {

        try {

            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
                'token' => 'required|string',
            ]);
        
            $email = $request->input('email');
            $password = $request->input('password');
            $token = $request->input('token');
        
            $passwordReset = DB::table('password_reset_tokens')
                ->where('email', $email)
                ->where('token', $token)
                ->first();
        
            if (!$passwordReset) {
                return response()->json([
                    'data' => null,
                    'message' => 'Email or Token is invalid ',
                    'success' => false
                ], 400);
            }
        
            $user = User::where('email', $email)->first();
            $user->password = Hash::make($password);
            $user->save();
        
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->where('token', $token)
                ->delete();
        
            return response()->json([
                'data' =>null,
                'message' => 'Password reset successful.',
                'success'=> true
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'data' => null,
                'message' => 'Something went wrong.',
                'success' => false
            ], 400);
        }

    }

    public function changePassword(Request $request)
{ 
    try {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);
    
        $user = Auth::user();
        $currentPassword = $request->input('current_password');
        $newPassword = $request->input('new_password');
    
        if (!Hash::check($currentPassword, $user->password)) {
            return response()->json([
                'data' => null,
                'message' => 'Incorrect current password',
                'success' => false
            ], 400);
        }
    
        // Current password is correct, update the user's password
        $user->password = Hash::make($newPassword);
        $user->save();
    
        return response()->json([
            'data' => null,
            'message' => 'Password changed successfully.',
            'success' => true,
        ], 200);
    } catch (\Throwable $th) {
        //throw $th;
        return response()->json([
            'data' => null,
            'message' => 'Something went wrong.',
            'success' => false
        ], 400);
    }

}
    
}
