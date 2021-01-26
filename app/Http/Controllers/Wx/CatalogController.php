<?php
namespace App\Http\Controllers\Wx;
use App\CodeReponse;
use App\Http\Services\CatalogServices;
use Illuminate\Http\Request;

class CatalogController extends WxController
{
    protected $only = [];

    public function index(Request $request){
        $id = $request->input('id',0);
        $l1List = CatalogServices::getInstance()->getL1List();
        if(empty($id)){
            $current = $l1List->first();
        }else{
            $current = $l1List->where('id',$id)->first();
        }
        $l2List = [];
        if(!is_null($current)){
            $l2List = CatalogServices::getInstance()->getL2ListByPid($current->id);
        }
        $data = [
            'categoryList'       => $l1List->toArray(),
            'currentCategory'    => $current,
            'currentSubCategory' => $l2List->toArray()
        ];
        return $this->success($data);
    }

    public function current(Request $request){
        $id = $request->input('id',0);
        if(empty($id)) return $this->fail(CodeReponse::PARAM_ILLEGAL);
        $category = CatalogServices::getInstance()->getL1ById($id);
        if(is_null($category)) return $this->fail(CodeReponse::PARAM_ILLEGAL);
        $l2List = CatalogServices::getInstance()->getL2ListByPid($category->id);
        $data = [
            'currentCategory'    => $category,
            'currentSubCategory' => $l2List->toArray()
        ];
        return $this->success($data);
    }
}
