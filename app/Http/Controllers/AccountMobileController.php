<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccountMobileController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Generate a token (you may want to use a stronger hashing method)
            $token = md5(time() . '.' . md5($request->email));

            // Save the token to the user's api_token field
            $user->forceFill([
                'api_token' => $token,
            ])->save();

            // Return the token in the JSON response
            return response()->json([
                'user_id' => $user->id,
                'token' => $token,
                
            ]);
        }

        // If authentication fails
        return response()->json([
            'message' => 'The provided credentials do not match our records.',
        ], 401); // Unauthorized status code
    }
}
