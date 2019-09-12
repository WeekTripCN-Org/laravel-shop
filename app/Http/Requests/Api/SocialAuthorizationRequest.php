<?php

namespace App\Http\Requests\Api;

class SocialAuthorizationRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // 只在其他指定任一字段不出现时，验证的字段才必须出现且不为空
        // 客户端要么提交授权码（code），要么提交 access_token 和 openid
        $rules = [
            'code'  => 'required_without:access_token|string',
            'access_token'  => 'required_without:code|string',
        ];
        
        // 微信登录 获取用户信息时必须传 openid
        if ($this->social_type == 'weixin' && !$this->code) {
            $rules['openid'] = 'required|string';
        }

        return $rules;
    }
}
