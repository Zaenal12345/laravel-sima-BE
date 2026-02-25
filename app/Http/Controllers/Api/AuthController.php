<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Events\UserRegistered;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            if(!Auth::attempt($request->only(['email','password']))) {
                return $this->unauthorized('Invalid email or password');
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token'.time())->plainTextToken;

            return $this->success('Login successful', [
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Throwable $th) {
            return $this->error('Login failed', $th->getMessage(), 500);
        }
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        array_merge($user->toArray(), ['password_plain' => $request->password]);

        $token = $user->createToken('api-token')->plainTextToken;

        event(new UserRegistered($user));

        return $this->success('Registration successful', [
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->success('Logout successful');
        } catch (\Throwable $th) {
            return $this->error('Logout failed', $th->getMessage(), 500);
        }
    }

}
