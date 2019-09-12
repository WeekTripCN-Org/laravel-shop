<?php

namespace App\Transformers;

use App\Models\Category;
use League\Fractal\TransformerAbstract;

/**
 * 分类数据转换层
 */
class CategoryTransformer extends TransformerAbstract
{
  public function transform(Category $category)
  {
    return [
      'id'        => $category->id,
      'name'      => $category->name,
      'parent_id'     => $category->parent_id,
      'is_directory'  => $category->is_directory,
      'level'         => $category->level,
      'path'         => $category->path,
    ];
  }
}