<?php

use App\Models\UserAddress;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User::all() 从数据获取所有的用户（我们之前通过 UsersSeeder 生成了 100 条），并返回一个集合 Collection。
        User::all()->each( function(User $user) {
            factory(UserAddress::class, random_int(1, 3))->create(['user_id' => $user->id]);
        });
    }
}
