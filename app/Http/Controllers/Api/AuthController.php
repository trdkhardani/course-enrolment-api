<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\HasApiTokens;

use App\Models\User;

class AuthController extends Controller
{
    public function authLogin(Request $request)
    {
        $credentials = $request->only('user_id_number', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 0,
                'message' => "Invalid id number or password"
            ], 401);
        }

        $user = User::where('user_id_number', $request->user_id_number)->first();
        $token = $user->createToken(Auth()->user()->user_id_number, [], now()->addMinutes(60));

        if (Auth::attempt($credentials) && Auth()->user()->user_role === 'student') {
            return response()->json([
                'status' => 1,
                'message' => "Welcome " . Auth()->user()->student->student_name,
                'role' => Auth()->user()->user_role,
                'department' => Auth()->user()->student->department->department_name,
                'token' => $token->plainTextToken
            ], 200);
        } elseif (Auth::attempt($credentials) && Auth()->user()->user_role === 'advisor') {
            return response()->json([
                'status' => 1,
                'message' => "Welcome " . Auth()->user()->advisor->advisor_name,
                'role' => Auth()->user()->user_role,
                'department' => Auth()->user()->advisor->department->department_name,
                'token' => $token->plainTextToken
            ], 200);
        } elseif (Auth::attempt($credentials) && Auth()->user()->user_role === 'admin') {
            return response()->json([
                'status' => 1,
                'message' => "Welcome Admin of " . Auth()->user()->admin->department->department_name . " Department",
                'role' => Auth()->user()->user_role,
                'token' => $token->plainTextToken
            ], 200);
        }
    }
}
