<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UpdateUserRequest;
use Exception;
use Illuminate\Http\Response;

class UserManagementController extends Controller
{
    public function index()
    {
        try {
            $users = User::all();

            return Response::json([
                'message' => "Users",
                'data' => $users
            ], 200);
        } catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $validated = $request->safe()->all();

            $response = $user->update($validated);

            if ($response) {
                return Response::json([
                    'message' => "User berhasil di update",
                    'data' => null
                ], 200);
            }

            return Response::json([
                'message' => "User gagal di update",
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
