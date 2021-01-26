<?php
namespace App\Models;
class Brand extends BaseModel
{
    protected $table = 'brand';

    protected $casts = [
        'floor_price' => 'float'
    ];
}
