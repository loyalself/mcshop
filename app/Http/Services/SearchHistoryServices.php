<?php
namespace App\Http\Services;
use App\Models\SearchHistory;

class SearchHistoryServices extends BaseServices
{
    /**
     * 保存用户的搜索历史记录
     * @param $userId
     * @param $keyword
     * @param $from
     * @return SearchHistory
     */
    public function save($userId,$keyword,$from){
        $history = new SearchHistory();
        $data = [
            'userId'  => $userId,
            'keyword' => $keyword,
            'from'    => $from
        ];
        $history->fill($data);
        $history->save();
        return $history;
    }
}
