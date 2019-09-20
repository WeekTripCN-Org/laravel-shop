<?php

namespace App\Http\Controllers\Api;

use App\Transformers\ProductTransformer;
use Illuminate\Http\Request;
use App\Http\Requests\Api\FavoriteRequest;
use App\Models\Product;

class FavoritesController extends Controller
{
    public function index()
    {
        $products = $this->user()->favoriteProducts()->paginate(10);
        return $this->response->paginator($products, new ProductTransformer());
    }

    public function store(FavoriteRequest $favoriteRequest)
    {
        $product_id = $favoriteRequest->product_id;
        $product = Product::find($product_id);
        if (!$this->user()->favoriteProducts()->find($product_id)) {
            $this->user()->favoriteProducts()->attach($product);
        }
        return $this->response->created();
    }

    public function destroy($product_id)
    {
        $product = Product::find($product_id);
        $this->user()->favoriteProducts()->detach($product);
        
        return $this->response->noContent();
    }
}
