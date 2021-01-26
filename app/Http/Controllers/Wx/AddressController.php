<?php
namespace App\Http\Controllers\Wx;
use App\CodeReponse;
use App\Http\Services\AddressServices;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AddressController extends WxController
{
    /**
     * 获取用户收货地址列表
     * @param int $userId
     * @return JsonResponse
     */
    public function getAddressListByUserId(int $userId){
        $addressLists = AddressServices::getInstance()->getAddressListByUserId($userId);
        $addressLists = $addressLists->map(function (Address $address){
            $address = $address->toArray();
            $item = [];
            foreach ($address as $key=>$value){
                $key = lcfirst(Str::studly($key));
                $item[$key] = $value;
            }
            return $item;
        });
        $data = [
            'total'        => $addressLists->count(),
            'page'         => 1,
            'addressLists' => $addressLists->toArray(),
            'pages'        => 1
        ];
        return $this->success($data);
    }
    /**
     * 删除用户地址
     * @param Request $request
     * @return JsonResponse
     * @throws \App\Exceptions\BusinessException
     */
    public function delete(Request $request){
        $id = $request->input('id',0);
        if(empty($id) && !is_numeric($id)){
            return $this->fail(CodeReponse::PARAM_ILLEGAL);
        }
        AddressServices::getInstance()->delete($this->user()->id,$id);
        return $this->success();
    }
}
