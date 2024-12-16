<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function register(RegisterRequest $request)
    {
        $validatedData =  $request->validated();

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),

        ]);
        $user->assignRole('user');

        $token = auth()->login($user);
        return $this->respondWithToken($token);
    } // end method


    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    } // end method


    public function me()
    {
        return response()->json([
            'user' => auth()->user(), // Menyertakan data user
            'role' => auth()->user()->getRoleNames()->first(), // Menyertakan role pertama pengguna
        ]);
    } // end method
    // end method


    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    } // end method


    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    } // end method


    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,

        ]);
    } // end method
}
