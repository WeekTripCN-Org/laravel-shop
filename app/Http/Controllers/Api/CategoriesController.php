<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Transformers\CategoryTransformer;
use App\Services\CategoryService;
use App\Http\Requests\Api\CategoryRequest;

class CategoriesController extends Controller
{
    public function index()
    {

        $category = CategoryService::getCategoryTree()->toArray();

        $data = $this->parseCategoryData($category);

        $return = ['data' => $data];

        return $this->response->array(($return));
    }

    /**
     * 根据父类获取对应的子类数据
     */
    public function show($category)
    {
        $category = CategoryService::getCategoryTree($category)->toArray();

        $data = $this->parseCategoryData($category);

        $return = ['data' => $data];

        return $this->response->array(($return));
    }

    /**
     * 重新组装数据
     */
    protected function parseCategoryData($category) 
    {
        $returnArr = $temp = [];
        foreach ($category as $v) {
            $temp['id'] = $v['id'];
            $temp['name'] = $v['name'];
            if (!empty($v['children'])) {
                foreach($v['children'] as $v2) {
                    $temp2 = array(
                        'id'        => $v2['id'],
                        'name'      => $v2['name'],
                        'parent_id' => $v2['parent_id'],
                    );
                    if (!empty($v2['children'])) {
                        foreach($v2['children'] as $v3) {
                            $temp3 = array(
                                'id'        => $v3['id'],
                                'name'      => $v3['name'],
                                'parent_id' => $v3['parent_id'],
                            );
                            $temp2['children'][] = $temp3;
                            $temp3 = [];
                        }
                        
                    }
                    $temp['children'][] = $temp2;
                    $temp2 = [];
                }
            }
            $returnArr[]=$temp;
            $temp = [];
        }
        return $returnArr;
    }
}
