<?php
namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class BaseModel extends Model
{
    //重写 model 默认维护的时间字段
    public const CREATED_AT = 'add_time';

    public const UPDATED_AT = 'update_time';

    /**
     * @return array
     */
    public function toArray(){
        $items = parent::toArray();
        $keys = array_keys($items);
        $keys = array_map(function ($key){
            return lcfirst(Str::studly($key));
        },$keys);
        $values = array_values($items);
        return array_combine($keys,$values);
    }
    /**
     * 重写时间格式化函数
     * @param \DateTimeInterface $date
     * @return string
     */
    public function serializeDate(\DateTimeInterface $date){
        return Carbon::instance($date)->toDateTimeString();
    }
}
