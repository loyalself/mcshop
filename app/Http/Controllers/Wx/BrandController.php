<?php


namespace App\Http\Controllers\Wx;


use App\CodeReponse;
use App\Http\Services\BrandServices;
use Illuminate\Http\Request;

class BrandController extends WxController
{
    protected $only = [];

    /**
     * 获取品牌列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request){
        $page = $request->input('page',1);
        $limit = $request->input('limit',10);
        $sort = $request->input('sort','add_time');
        $order = $request->input('order','desc');

        $columns = ['id','name','desc','pic_url','floor_price'];
        $list = BrandServices::getInstance()->getBrandList($page,$limit,$sort,$order,$columns);
        return $this->successPaginate($list);
    }
    /**
     * 获取某个品牌的详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(Request $request){
        $id = $request->input('id',0);
        if(empty($id)) return $this->fail(CodeReponse::PARAM_ILLEGAL);
        $brand = BrandServices::getInstance()->getBrand($id);
        if(is_null($brand)) return $this->fail(CodeReponse::PARAM_ILLEGAL);
        return  $this->success($brand);
    }

}
