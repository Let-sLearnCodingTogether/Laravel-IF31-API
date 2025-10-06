<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ProfileController extends Controller
{
    public function profile() {
        return Response::json([
            'message' => 'profile',
            'data' => Auth::user()
        ], 200);
    }
}
