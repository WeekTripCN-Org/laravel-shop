<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
/*
id	自增长 ID	unsigned big int	主键
name	优惠券的标题	varchar	无
code	优惠码，用户下单时输入	varchar	唯一
type	优惠券类型，支持固定金额和百分比折扣	varchar	无
value	折扣值，根据不同类型含义不同	decimal	无
total	全站可兑换的数量	unsigned int	无
used	当前已兑换的数量	unsigned int, default 0	无
min_amount	使用该优惠券的最低订单金额	decimal	无
not_before	在这个时间之前不可用	datetime, null	无
not_after	在这个时间之后不可用	datetime, null	无
enabled	优惠券是否生效	tinyint	无
*/
class CreateCouponCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('优惠券的标题');
            $table->string('code')->unique()->comment('优惠码，用户下单时输入');
            $table->string('type')->comment('优惠券类型，支持固定金额和百分比折扣');
            $table->decimal('value')->comment('折扣值');
            $table->unsignedInteger('total')->comment('全站可兑换的数量');
            $table->unsignedInteger('used')->default(0)->comment('当前已兑换的数量');
            $table->decimal('min_amount', 10, 2)->comment('使用该优惠券的最低订单金额');
            $table->datetime('not_before')->nullable()->comment('在这个时间之前不可用');
            $table->datetime('not_after')->nullable()->comment('在这个时间之后不可用');
            $table->boolean('enabled')->comment('优惠券是否生效');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupon_codes');
    }
}
