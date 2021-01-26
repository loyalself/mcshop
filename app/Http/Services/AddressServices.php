<?php


namespace App\Http\Services;


use App\CodeReponse;
use App\Exceptions\BusinessException;
use App\Models\Address;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AddressServices extends BaseServices
{
    /**
     * 根据用户 id 获取收货地址列表
     * @param int $userId
     * @return Address[]|Collection
     */
    public function getAddressListByUserId(int $userId){
        return Address::query()->where('id',$userId)->where('deleted',0)->get();
    }
    /**
     * 根据用户id以及地址id,获取某个地址的详情
     * @param int $userId
     * @param int $addressId
     * @return Builder|Model|object|null
     */
    public function getAddress(int $userId,int $addressId){
        return Address::query()->where([
            'user_id'    => $userId,
            'address_id' => $addressId,
            'deleted'    => 0
        ])->first();
    }

    /**
     * 删除地址
     * @param int $userId
     * @param int $addressId
     * @return bool|mixed|null
     * @throws BusinessException
     */
    public function delete(int $userId,int $addressId){
        $address = $this->getAddress($userId,$addressId);
        if(is_null($address)){
           //throw new BusinessException(CodeReponse::PARAM_ILLEGAL);
            $this->throwBusinessException(CodeReponse::PARAM_ILLEGAL);
        }
        return $address->delete();
    }
}
