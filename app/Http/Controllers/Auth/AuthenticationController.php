<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class AuthenticationController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->safe()->all();

            if (!Auth::attempt($validated)) {
                return Response::json([
                    'message' => "Email atau password tidak valid",
                    'data' => null
                ], 401);
            }

            $user = $request->user();
            $token = $user->createToken('learn_laravel_api', [])->plainTextToken;

            return Response::json([
                'message' => 'Login berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role
                    ],
                    'token' => $token
                ]
            ], 200);
        } catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->safe()->all();
            $validated['password'] = Hash::make($validated['password']);

            $response = User::create($validated);

            if ($response) {
                return Response::json([
                    'message' => 'Berhasil register user baru, silahkan login',
                    'data' => null
                ], 201);
            }

            return Response::json([
                'message' => 'Gagal membuat user',
                'data' => null
            ], 400);
        } catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return Response::json([
                'message' => 'Berhasil logout',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}