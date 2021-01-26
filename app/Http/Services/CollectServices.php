<?php
namespace App\Http\Services;
use App\Constant;
use App\Models\Collect;

class CollectServices extends BaseServices
{
    public function countByGoodsId($userId,$goodsId){
        return Collect::query()->where([
            'user_id'  => $userId,
            'value_id' => $goodsId,
            'type'     => Constant::COLLECT_TYPE_GOODS
        ])->count();
    }
}
