<?php

namespace App\Http\Controllers;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\FlareClient\Api;


class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    // TODO: Đăng nhập
    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        // validate email and password
        $credentials = $request->only('email', 'password');
        Log::info('Request data:', $credentials);

        if (! $token = auth()->attempt($credentials)) {

            return ApiResponse::error([], ApiMessage::ERROR, 401);
        }

        return $this->createNewToken($token);
    }

    // TODO: Tạo Tài khoản mới
    public function register(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        $input = $request->all();
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }


    // TODO: Đăng xuất
    public function logout(): \Illuminate\Http\JsonResponse
    {
        auth()->logout();
        return ApiResponse::success([], ApiMessage::LOGOUT_SUCCESS, 200);
    }



    // TODO: Lấy thong tin người dùng
    public function userProfile(): \Illuminate\Http\JsonResponse
    {
        return ApiResponse::success(auth()->user(), ApiMessage::USER_PROFILE, 200);
    }

    protected function createNewToken($token): \Illuminate\Http\JsonResponse
    {
        return ApiResponse::success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 6000,
            'profile' => auth()->user()->only(['id', 'name', 'email'])
        ], ApiMessage::SUCCESS, 201);
    }

    // TODO: Refresh token
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
}
