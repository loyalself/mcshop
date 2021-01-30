<?php
namespace App\Inputs;
use App\Http\Traits\VerifyRequestInput;
use Illuminate\Validation\Rule;

/*class GoodsListInput
{
    use VerifyRequestInput;

    public $categoryId;
    public $brandId;
    public $keyword;
    public $isNew;
    public $isHot;
    public $page;
    public $limit;
    public $sort = 'add_time';
    public $order = 'desc';

    public function fill(){
        $this->categoryId = $this->verifyId('categoryId');
        $this->brandId = $this->verifyId('brandId');
        $this->keyword = $this->verifyString('keyword');
        $this->isNew = $this->verifyBoolean('isNew');
        $this->isHot = $this->verifyBoolean('isHot');
        $this->page = $this->verifyInteger('page',1);
        $this->limit = $this->verifyInteger('limit',10);
        $this->sort = $this->verifyEnum('sort','add_time',['add_time','retail_price','name']);
        $this->order = $this->verifyEnum('order','desc',['desc','asc']);
        return $this;
    }

    public static function new(){
        return (new static())->fill();
    }
}*/
class GoodsListInput extends BaseInput
{
    public $categoryId;
    public $brandId;
    public $keyword;
    public $isNew;
    public $isHot;
    public $page;
    public $limit;
    public $sort = 'add_time';
    public $order = 'desc';

    /*public function fill(){
        $this->categoryId = $this->verifyId('categoryId');
        $this->brandId = $this->verifyId('brandId');
        $this->keyword = $this->verifyString('keyword');
        $this->isNew = $this->verifyBoolean('isNew');
        $this->isHot = $this->verifyBoolean('isHot');
        $this->page = $this->verifyInteger('page',1);
        $this->limit = $this->verifyInteger('limit',10);
        $this->sort = $this->verifyEnum('sort','add_time',['add_time','retail_price','name']);
        $this->order = $this->verifyEnum('order','desc',['desc','asc']);
        return $this;
    }*/

    public function rules(){
        return [
            'categoryId' => 'integer|digits_between:1,20',
            'brandId' => 'integer|digits_between:1,20',
            'keyword' => 'string',
            'isNew' => 'boolean',
            'isHot' => 'boolean',
            'page' => 'integer',
            'limit' => 'integer',
            'sort' => Rule::in(['add_time','retail_price','name']),
            'order' => Rule::in(['desc','asc']),
        ];
    }
}
