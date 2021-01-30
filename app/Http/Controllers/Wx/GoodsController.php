<?php
namespace App\Http\Controllers\Wx;
use App\CodeReponse;
use App\Constant;
use App\Http\Services\BrandServices;
use App\Http\Services\CatalogServices;
use App\Http\Services\CollectServices;
use App\Http\Services\CommentServices;
use App\Http\Services\GoodsServices;
use App\Http\Services\SearchHistoryServices;
use Illuminate\Http\Request;

class GoodsController extends WxController
{
    protected $only = [];

    public function count(){
        $count = GoodsServices::getInstance()->countGoodsSale();
        return $this->success($count);
    }

    /**
     * 获取某个分类下的商品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function category(Request $request){
        /*$id = $request->input('id',0);
        if(empty($id)) return $this->fail(CodeReponse::PARAM_ILLEGAL);*/
        $id = $this->verifyId('id');

        $cur = CatalogServices::getInstance()->getCategory($id);
        if(empty($cur)) return $this->fail(CodeReponse::PARAM_ILLEGAL);

        $parent = null;
        $children = null;
        if($cur->pid == 0){
            $parent = $cur;
            $children = CatalogServices::getInstance()->getL2ListByPid($cur->id);
            $cur = $children->first() ?? $cur;
        }else{ //子类目,二级类目
            $parent = CatalogServices::getInstance()->getL1ById($cur->pid);
            $children = CatalogServices::getInstance()->getL2ListByPid($cur->pid);
        }

        $data = [
            'currentCategory' => $cur,
            'parentCategory'  => $parent,
            'brotherCategory' => $children
        ];
        return  $this->success($data);
    }

    public function list(){
        //验证参数
       /*$input = $request->validate([
            'category_id' => 'required|integer'
        ]);*/
        //问题:当验证完后,要用 categpry_id 还需要再取一次,即  $categoryId = $input['category_id],这样很不方便,优化:
        $categoryId = $this->verifyId('categoryId');
        $brandId = $this->verifyId('brandId');
        $keyword = $this->verifyString('keyword');
        $isNew = $this->verifyBoolean('isNew');
        $isHot = $this->verifyBoolean('isHot');
        $page = $this->verifyInteger('page',1);
        $limit = $this->verifyInteger('limit',10);
        $sort = $this->verifyEnum('sort','add_time',['add_time','retail_price','name']);
        $order = $this->verifyEnum('order','desc',['desc','asc']);


       /* $categoryId = $request->input('categoryId');
        $brandId = $request->input('brandId');
        $keyword = $request->input('keyword');
        $isNew = $request->input('isNew');
        $isHot = $request->input('isHot');
        $page = $request->input('page',1);
        $limit = $request->input('limit',10);
        $sort = $request->input('sort','add_time');
        $order = $request->input('order','desc');*/


        if($this->isLogin() && !empty($keyword)){ //如果已登录或者搜索词不为空
            SearchHistoryServices::getInstance()->save($this->userId(),$keyword,Constant::SEARCH_HISTORY_FROM_WX);
        }
        $goodsList = GoodsServices::getInstance()->listGoods($categoryId,$brandId,$keyword,$isNew,$isHot,$page, $limit,$sort,$order);
        $categoryList = GoodsServices::getInstance()->listL2Category($brandId,$isNew,$isHot,$keyword);
        $goodsList = $this->paginate($goodsList);
        $goodsList['filterCategoryList'] = $categoryList;
        return $this->success($goodsList);
    }

    public function detail(Request $request){
        $id = $request->input('id',0);
        if(empty($id)) return $this->fail(CodeReponse::PARAM_ILLEGAL);
        $info = GoodsServices::getInstance()->getGoods($id);
        if(empty($info)) return $this->fail(CodeReponse::PARAM_ILLEGAL);
        $attr = GoodsServices::getInstance()->getGoodsAttribute($id);
        $spec = GoodsServices::getInstance()->getGoodsSpecification($id);
        $product = GoodsServices::getInstance()->getGoodsProduct($id);
        $issue = GoodsServices::getInstance()->getGoodsIssue();
        /**
         * 如果当前商品的品牌 id 不为空,就返回品牌信息,否则返回一个对象;返回对象的两种方式:
         *  1. new \StdClass 2. (object)[] 将数组强制转换一下
         */
        $brand = $info->brand_id ? BrandServices::getInstance()->getBrand($info->brand_id): (object)[] ;
        $comment = CommentServices::getInstance()->getCommentWithUserInfo($id);
        $userHasCollect = 0;
        if($this->isLogin()){
            $userHasCollect = CollectServices::getInstance()->countByGoodsId($this->userId(),$id);
            //保存用户访问足迹
            GoodsServices::getInstance()->saveFootprint($this->userId(),$id);
        }
        $data = [
            'info'           => $info,
            'attr'           => $attr,
            'spec'           => $spec,
            'issues'         => $issue,
            'product'        => $product,
            'brand'          => $brand,
            'comment'        => $comment,
            'userHasCollect' => $userHasCollect,
            'share'          => false,
            'shareImage'     => $info->share_url,
            'groupon'        => [] //团购信息还没做
        ];
        return $this->success($data);
    }
}
