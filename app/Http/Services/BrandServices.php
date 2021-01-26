<?php


namespace App\Http\Services;


use App\Models\Brand;
use Illuminate\Database\Eloquent\Builder;

class BrandServices extends BaseServices
{
    public function getBrand(int $id){
        return Brand::query()->find($id);
    }

    public function getBrandList(int $page,int $limit,$sort,$order,$columns = ['*']){
       //写法一
        /*return Brand::query()->where('deleted',0)
            ->when(!empty($sort) && !empty($order),function (Builder $builder) use($sort,$order){
                return $builder->orderBy($sort,$order);
            })->paginate($limit,$columns,'page',$page);*/

        //写法二
        $query = Brand::query()->where('deleted',0);
        if(!empty($sort) && !empty($order)){
            $query = $query->orderBy($sort,$order);
        }
        return $query->paginate($limit,$columns,'page',$page);
    }
}
