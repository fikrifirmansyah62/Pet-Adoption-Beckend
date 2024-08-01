<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            // validasi input
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                return $this->sendError('Unauthorized', 'Authentication Failed', 500);
            }

            // cek email
            $user = User::where('email', $request->email)->first();
            // cek password
            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            // jika berhasil maka akan login
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return $this->sendResponse([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');
        } catch (Exception $error) {
            return $this->sendError([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Login Failed');
        }
    }

    // register account
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|min:8'
            ]);

            //insert data user
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $user = User::where('email', $request->email)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            $data = [
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ];

            return $this->sendResponse($data, 'Successfully registered');
        } catch (Exception $error) {
            return $this->sendError([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Registration Failed');
        }
    }

    public function show(User $user)
    {
        try {
            $user = Auth::user();

            return $this->sendResponse($user, 'User data get successfully');
        } catch (Exception $error) {
            return $this->sendError([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'User get failed');
        }
    }

    public function logout()
    {
        $user = User::find(Auth::user()->id);

        $user->tokens()->delete();

        return response()->noContent();
    }
}
