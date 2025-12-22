<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;

class AuthController extends Controller
{
    /**
     * Handle user registration
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user =  User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            Log::info('New user registered', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        } catch (\Throwable $error) {
            Log::error('Registration Error: ' . $error->getMessage());
            return response()->json(['message' => 'Registration failed', 'error' => $error], 500);
        }

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    /**
     * Handle user login
     *
     * @param Request $request
     * @return string $token 
     */
    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = User::find(Auth::user()->id);
            $token = $user->createToken('auth_token')->accessToken;
            return response()->json(['message' => 'Login successful', 'token' => $token], 200);
        } else {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }
    }

    /**
     * Handle user logout
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $token = $request->user()->token();

            if ($token) {
                $token->revoke();
            }

            if ($token->refreshToken) {
                $token->refreshToken->revoke();
            }

            return response()->json(['message' => 'Logout successful'], 200);
        } else {
            return response()->json(['message' => 'No authenticated user found'], 401);
        }
    }
}
