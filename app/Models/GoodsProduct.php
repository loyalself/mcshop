<?php


namespace App\Models;


class GoodsProduct extends BaseModel
{
    protected $table = 'goods_product';

    protected $casts  = [
        'deleted'        => 'boolean',
        'specifications' => 'array',
        'price'          => 'float'
    ];
}
