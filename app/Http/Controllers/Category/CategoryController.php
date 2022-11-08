<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonUtility;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;

class CategoryController extends CommonUtility
{
    //
    public function get_all_categories(){
        $categoris = Category::all();
        if(count($categoris)>0){
            return $this->get_response(null, 'One or more category found.', ['categories'=>$categoris], 200);
        }
        return $this->get_response(true, 'No category found.', ['categories'=>''], 404);
    }
    public function get_all_subcategories(){
        $sub_categoris = SubCategory::all();
        if(count($sub_categoris)>0){
            return $this->get_response(null, 'One or more sub-category found.', ['sub_categoris'=>$sub_categoris], 200);
        }
        return $this->get_response(true, 'No sub-category found.', ['sub_categoris'=>''], 404);
    }
}
