<?php

use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = factory(\App\Models\Product::class, 30)->create();
        foreach ($products as $product) {
            // 创建3个 SKU，并且每个 SKU 的 product_id 字段都设为当前循环的商品ID
            $skus = factory(\App\Models\ProductSku::class, 3)->create(['product_id' => $product->id]);
            // 找出价格最低的 SKU 价格
            $product->update(['price' => $skus->min('price')]);
        }
    }
}
