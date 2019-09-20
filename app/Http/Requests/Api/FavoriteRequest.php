<?php

namespace App\Http\Requests\Api;

class FavoriteRequest extends FormRequest
{
    public function rules()
    {
        return [
            'product_id'    => 'required'
        ];
    }
}
