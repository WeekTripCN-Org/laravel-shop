<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'province',
        'city',
        'district',
        'address',
        'zip',
        'contact_name',
        'contact_phone',
        'last_used_at',
    ];

    protected $dates = ['last_used_at'];    // 时间日期类型

    protected $appends = ['full_address'];

    /**
     * 一对多 关联关系
     * 一个 User 可以有多个 UserAddress , 一个 UserAddress 只能属于一个 User 。
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取完整的地址
     */
    public function getFullAddressAttribute()
    {
        return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }
}
