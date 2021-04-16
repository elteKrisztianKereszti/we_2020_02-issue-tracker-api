<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TokenController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'name' => 'required',
        ]);

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            throw ValidationException::withMessages([
                'message' => 'Email used'
            ]);
        }

        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user['password'])) {
            throw ValidationException::withMessages([
                'message' => 'Invalid cred.'
            ]);
        }

        $token = $user->createToken("token");

        return [ 'token' => $token->plainTextToken ];
    }

    public function logout(Request $request)
    {
        $user = auth()->user();

        foreach ($user->tokens as $token) {
            $token->delete();
        }

        return response()->json(['message' => 'Logout run'], 200);
    }
}
