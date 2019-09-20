<?php

namespace App\Http\Controllers\Api;

use App\Models\UserAddress;
use App\Transformers\UserAddressTransformer;
use App\Http\Requests\Api\UserAddressRequest;
use App\Models\User;

class UserAddressesController extends Controller
{
    public function store(UserAddressRequest $request, UserAddress $userAddress)
    {
        // $userAddress = $request->user()->addresses()->create($request->only([
        //     'province',
        //     'city',
        //     'district',
        //     'address',
        //     'zip',
        //     'contact_name',
        //     'contact_phone'
        // ]));

        $userAddress->province      = $request->province;
        $userAddress->city          = $request->city;
        $userAddress->district      = $request->district;
        $userAddress->address       = $request->address;
        $userAddress->zip           = $request->zip;
        $userAddress->contact_name  = $request->contact_name;
        $userAddress->contact_phone = $request->contact_phone;
        $userAddress->user()->associate($this->user());

        $userAddress->save();

        return $this->response->item($userAddress, new UserAddressTransformer())->setStatusCode(201);
    }

    public function index()
    {
        $addresses = $this->user()->addresses;
        return $this->response->collection($addresses, new UserAddressTransformer());
    }
}
