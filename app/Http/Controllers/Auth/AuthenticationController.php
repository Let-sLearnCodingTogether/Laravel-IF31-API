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
                    'message' => 'Invalid credentials',
                    'data' => null
                ], 401);
            }

            $user = $request->user();

            $token = $user->createToken('auth_token')->plainTextToken;

            return Response::json([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'access_token' => $token,
            ], 200);
        } catch (Exception $e) {
            return Response::json([
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->safe()->all();

            $passwordHash = Hash::make($validated['password']);

            $validated['password'] = $passwordHash;

            $response = User::create($validated);

            if ($response) {
                return Response::json([
                    'message' => 'User registered successfully',
                    'data' => $response
                ], 201);
            }
        } catch (Exception $e) {
            return Response::json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Ambil user yang sedang login 
            // ambil tokennya terus hapus
            $request->user()->currentAccessToken()->delete();

            // berikan response jika berhasil logout
            return response()->json([
                'message' => 'Berhasil Logout',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
