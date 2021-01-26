<?php
namespace App\Http\Services;
use App\Models\Footprint;
use App\Models\Goods;
use App\Models\GoodsAttribute;
use App\Models\GoodsProduct;
use App\Models\GoodsSpecification;
use App\Models\Issue;
use Illuminate\Database\Eloquent\Builder;

class GoodsServices extends BaseServices
{
    /**
     * 根据商品 id 获取商品的详情
     * @param int $id
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getGoods(int $id){
        return Goods::query()->where('deleted',0)->find($id);
    }
    /**
     * 根据商品id获取商品的属性
     * @param int $goodsId
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getGoodsAttribute(int $goodsId){
        return GoodsAttribute::query()->where([
            'goods_id' => $goodsId,
            'deleted'  => 0
        ])->get();
    }
    /**
     * 根据商品 id 获取商品的规格
     * @param int $goodsId
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getGoodsSpecification(int $goodsId){
        $specs = GoodsSpecification::query()->where([
            'goods_id' => $goodsId,
            'deleted'  => 0
        ])->groupBy('specification')->get();
        return $specs->map(function ($v,$k){
            return ['name'=>$k,'valueList'=>$v];
        })->values();
    }

    public function getGoodsProduct(int $goodsId){
        return GoodsProduct::query()->where([
            'goods_id' => $goodsId,
            'deleted'  => 0
        ])->get();
    }

    public function saveFootprint(int $userId,int $goodsId){
        $footPrint = new Footprint();
        $footPrint->fill([
            'user_id'  => $userId,
            'goods_id' => $goodsId
        ]);
        return $footPrint->save();
    }

    /**
     * 获取商品的常见问题
     * @param int $page
     * @param int $limit
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getGoodsIssue(int $page = 1,int $limit = 4){
        return Issue::query()->where('deleted',0)->forPage($page,$limit)->get();
    }

    /**
     * 获取在售商品的数量
     * @return int
     */
    public function countGoodsSale(){
        return Goods::query()->where([
            'is_on_sale' => 1,
            'deleted'    => 0
        ])->count('id');
    }

    public function listGoods($categoryId,$brandId,$isNew,$isHot,$keyword,$sort = 'add_time',$order = 'desc',$page = 1,$limit = 10){
        $query = $this->getQueryByGoodsFilter($brandId,$isNew,$isHot,$keyword);
        if(!empty($categoryId)){
            $query->where('category_id',$categoryId);
        }
        return $query->orderBy($sort,$order)->paginate($limit,['*'],'page',$page);
    }

    public function listL2Category($brandId,$isNew,$isHot,$keyword){
        $query = $this->getQueryByGoodsFilter($brandId,$isNew,$isHot,$keyword);
        $categoryIds = $query->select(['category_id'])->pluck('category_id')->unique()->toArray();
        return CatalogServices::getInstance()->getL2ListByPid($categoryIds);
    }

    private function getQueryByGoodsFilter($brandId,$isNew,$isHot,$keyword){
        $query = Goods::query()->where([
            'is_on_sale' => 1,
            'deleted'    => 0
        ]);
        if(!empty($brandId)){
            $query->where('brand_id',$brandId);
        }
        if(!empty($isNew)){
            $query->where('is_new',$isNew);
        }
        if(!empty($isHot)){
            $query->where('is_hot',$isHot);
        }
        if(!empty($keyword)){
            $query = $query->where(function(Builder $builder) use ($keyword){
                $builder->where('keywords','like',"$keyword")
                    ->orWhere('name','like',"$keyword");
            });
        }
        return $query;
    }
}
