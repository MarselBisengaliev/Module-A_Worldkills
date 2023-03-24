<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{

    public function register(Request $request)
    {
        $passwordMaxLength = pow(2, 16);
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username|min:4|max:60',
            'password' => "required|min:8|max:$passwordMaxLength"
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return response()->json([
                'errors' => $errors
            ], 404);
        }

        $validated = $validator->validated();

        $user = User::create([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'registered_timestamp' => Date::now()->format('Y-m-d H:i:s'),
            'last_login_timestamp' => Date::now()->format('Y-m-d H:i:s')
        ]);

        $expires_at = Date::now()->addYear();

        $token = $user->createToken('myapptoken', ['*'], $expires_at)->plainTextToken;

        return response()->json([
            'token' => $token,
            'expires_at' => $expires_at,
            'status' => 'success'
        ], 200);
    }

    public function login(Request $request)
    {
        $passwordMaxLength = pow(2, 16);
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:4|max:60',
            'password' => "required|min:8|max:$passwordMaxLength"
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return response()->json([
                'errors' => $errors
            ], 404);
        }

        $validated = $validator->validated();

        if (!Auth::attempt($validated)) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Wrong username or password'
            ], 401);
        }

        $user = User::query()
            ->where('username', $validated['username'])
            ->first();

        $expires_at = Date::now()->addYear();

        $token = $user->createToken('myapptoken', ['*'], $expires_at)->plainTextToken;
        return response()->json([
            'status' => 'success',
            'token' => $token
        ], 200);
    }

    public function logout() {
        auth()->user()->tokens()->delete();

        return response()->json([
            'status' => 'success'
        ]);
    }
}
