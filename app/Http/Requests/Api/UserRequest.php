<?php

namespace App\Http\Requests\Api;

class UserRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'      => 'required|between:2,25|regex:/^[[\x7f-\xffA-Za-z0-9\-\_]+$/|unique:users,name', // 支持中文
            'password'  => 'required|string|min:6',
            'verification_key'  => 'required|string',
            'verification_code' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'verification_key'  => '短信验证码 key',
            'verification_code' => '短信验证码',
        ];
    }
}
