<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user (Admin, Pembimbing, Mahasiswa)
     */
    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'status'  => false,
            'message' => 'Email atau password salah.'
        ], 401);
    }

    // Ambil nama role
    $roleName = $user->role->name ?? null; // admin / pembimbing / user

    // Token Sanctum
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'status'  => true,
        'message' => 'Login berhasil.',
        'token'   => $token,
        'user'    => [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $roleName,
        ]
    ]);
}

    /**
     * Logout
     */
    public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'status' => true,
        'message' => 'Logged out successfully'
    ]);
}
}