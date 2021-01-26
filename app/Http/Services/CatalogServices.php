<?php
namespace App\Http\Services;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CatalogServices extends BaseServices
{
    /**
     * @return Category[]|Collection
     */
    public function getL1List(){
        return Category::query()->where([
            'level'   => 'L1',
            'deleted' => 0
        ])->get();
    }
    public function getL1ById(int $id){
        return Category::query()->where([
            'level'   => 'L1',
            'deleted' => 0,
            'id'      => $id
        ])->first();
    }
    /**
     * @param int $pid
     * @return Category[]|Collection
     */
    public function getL2ListByPid(int $pid){
        return Category::query()->where([
            'level'   => 'L2',
            'deleted' => 0,
            'pid'     => $pid
        ])->get();
    }

    public function getCategory(int $id){
        return Category::query()->find($id);
    }

    public function getL2ByIds(array $ids){
        if(empty($ids)) return collect([]);
        return Category::query()->whereIn('id',$ids)->get();
    }
}
