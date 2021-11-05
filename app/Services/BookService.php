<?php 

namespace App\Services;

use App\Models\Book;
use Illuminate\Http\Request;

class BookService{

    protected function filterBooks($query , $filters){
                
        // 把filters 中的 @XX@ 分割 並 轉為 collect型態
        $filters = collect(explode('@XX@', $filters));

        // 類似 foreach 將每筆 filter 讀出
        $filters->map(function($filter) use($query){
            
            // 分割filter 的 key value
            list($key,$value) = explode(':', $filter);

            // 查詢建構器 加上 where key value 條件
            $query->where($key, 'like', "%$value%");
        });

        return $query;
    }

    protected function sortBooks($query ,$sorts){
        $sorts = collect(explode('@XX@', $sorts));

        $sorts->map(function($sort) use($query){
            list($key,$value) = explode(':', $sort);
            
            if(in_array($value, ['asc','desc','ASC','DESC'])){
                $query->orderBy($key, $value);
            }
        });

        return $query;
    }

    /**
     * Return all book data
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Controllers\Api\V1\Book\BookController  request()->query()
     * @return $books
     */

    public function getListData($request, $queryParams){
        // 產生查詢建構器，並用預處理with方法讀取關聯type資料表
        $books_query = Book::query()->with('type');
        
        /* ----------------------篩選條件 filters 參數----------------------- */
        
        $books_query->when(!is_null(request('filters')), function ($q) {
            
            $this->filterBooks($q,request('filters'));
        });

        /* ----------------------排列順序 filters 參數----------------------- */

        $books_query->when(!is_null(request('sorts')), function ($q) {

            $this->sortBooks($q,request('sorts'));
        }, function($q) {

            $q->orderBy('id','desc');
        });

        // 如果有分頁的筆數 再加上 否則預設抓所有
        
        $books = request('limit') ? $books_query->paginate(request('limit'))->appends($queryParams) : $books_query->get();

        return $books;
    }
}

?>