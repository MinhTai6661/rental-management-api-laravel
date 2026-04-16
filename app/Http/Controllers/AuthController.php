<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'message' => 'Chào mừng Admin, đây là danh sách phòng bí mật của MyRent',
            'user' => $request->user()
        ]);
    }

    private function generateTokenForUser(User $user)
    {
        $token = $user->createToken('auth_token');

        return $token->plainTextToken;
    }

    private function validateUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                    'email' => ['Thông tin đăng nhập không chính xác.'],
                ]);
        }
        return $user;
    }

    public function register(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Create token
        $token = $user->createToken('auth_token');

        return response()->json([
            'message' => 'User registered successfully',
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        $user = $this->validateUser($request);
        $token = $this->generateTokenForUser($user);

        return ['token' => $token, 'user' => $user];
    }
}
