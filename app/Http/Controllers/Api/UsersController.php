<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\Api\UserRequest;
use App\Models\User;
use App\Transformers\UserTransformer;
use App\Models\Image;

class UsersController extends Controller
{
    public function store(UserRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        // hash_equals 是可防止时序攻击的字符串比较
        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回 401
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user = User::create([
            'name'  => $request->name,
            'phone' => $verifyData['phone'],
            'password'  => bcrypt($request->password)
        ]);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);
        
        return $this->response->item($user, new UserTransformer())
            ->setMeta([
                'access_token'  => \Auth::guard('api')->fromUser($user),
                'token_type'    => 'Bearer',
                'expires_in'    => \Auth::guard('api')->factory()->getTTL() * 60
            ])
            ->setStatusCode(201);
    }

    public function me()
    {
        return $this->response->item($this->user(), new UserTransformer());
    }

    public function update(UserRequest $request)
    {
        $user = $this->user();

        $attibutes = $request->only(['name', 'email', 'registration_id']);
        
        if ($request->avatar_image_id) {
            $image = Image::find($request->avatar_image_id);

            $attibutes['avatar'] = $image->path;
        }

        $user->update($attibutes);
        return $this->response->item($user, new UserTransformer());
    }
}
