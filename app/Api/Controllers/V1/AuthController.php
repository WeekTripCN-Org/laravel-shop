<?php

namespace App\Api\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

/**
 * 用户相关接口
 */
class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('refresh', ['except' => ['login', 'register']]);
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => '登录失败'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register()
    {
        $rules = [
            'name'      => ['required'],
            'password'  => ['required', 'min:6', 'max:16'],
            'email'     => ['required', 'unique:users'],
        ];

        $postData = request(['name', 'email', 'password']);
        $validator = Validator::make($postData, $rules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        // 创建用户
        $result = User::create([
            'name'  => $postData['name'],
            'email' => $postData['email'],
            'password'  => bcrypt($postData['password'])
        ]);

        if ($result) {
            return response()->json(['success' => '创建用户成功']);
        } else {
            return response()->json(['error' => '创建用户失败']);
        }
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => '退出成功']);
    }

    /**
     * 刷新token，如果开启黑名单，以前的token便会失效。
     * 值得注意的是用上面的getToken再获取一次Token并不算做刷新，两次获得的Token是并行的，即两个都可用。
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token'  => $token,
            'token_type'    => 'bearer',
            'expires_in'    => auth('api')->factory()->getTTL() * 60
        ]);
    }



}
