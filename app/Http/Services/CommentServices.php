<?php
namespace App\Http\Services;
use App\Models\Comment;
use Illuminate\Support\Arr;

class CommentServices extends BaseServices
{
    /**
     * 根据商品id 获取商品的评论
     * @param $goodsId
     * @param int $page
     * @param int $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCommentByGoodsId($goodsId,$page = 1,$limit = 2){
        return Comment::query()->where([
            'value_id' => $goodsId,
            'type'     => 0,
            'deleted'  => 0
        ])->paginate($limit,['*'],'page',$page);
    }

    public function getCommentWithUserInfo($goodsId,$page = 1,$limit = 2){
        $comments = $this->getCommentByGoodsId($goodsId,$page,$limit);
        $userIds = Arr::pluck($comments->items(),'user_id');
        $userIds = array_unique($userIds);
        //一条sql批量获取用户信息,避免 foreach 循环查询
        $users = UserServices::getInstance()->getUsers($userIds)->keyBy('id');
        $data = collect($comments->items())->map(function (Comment $comment) use ($users){
            $user = $users->get($comment->user_id);
           /* return [
                'id'           => $comment->id,
                'addTime'      => $comment->add_time,
                'content'      => $comment->content,
                'adminContent' => $comment->admin_content,
                'picList'      => $comment->pic_urls,
                'nickname'     => $user->nickname,
                'avatar'       => $user->avatar
            ];*/
            $comment = $comment->toArray();
            $comment['picList'] = $comment['picUrls'];
            $comment = Arr::only($comment,['id','addTime','content','adminContent','picList']);
            $comment['nickname'] = $user->nickname ?? '';
            $comment['avatar'] = $user->avatar ?? '';
            return $comment;
        });
        return [
            'count' => $comments->total(),
            'data'  => $data
        ];
    }
}
