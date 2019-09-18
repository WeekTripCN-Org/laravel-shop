<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductSku;
use App\Transformers\ProductTransformer;

class ProductsController extends Controller
{
    public function index($category, Product $product)
    {
        if (Category::find($category)) {
            $query = $product->query();
            $query->where('category_id', $category);
            $query->where('on_sale', true);         // 在售商品
            $query->orderBy('created_at', 'desc');  

            $products = $query->paginate(10);

            return $this->response->paginator($products, new ProductTransformer());
        }
        return $this->response->errorBadRequest('未找到对应的分类');
    }

    public function show($id, Product $product)
    {
        $product = $product::find($id);
        if ($product) {
            return $this->response->item($product, new ProductTransformer());
        }
        return $this->response->errorBadRequest('未找到对应的商品');
        
    }
}
