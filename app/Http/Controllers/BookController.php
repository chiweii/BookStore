<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $url = request()->url();
        $queryParams = request()->query(); 
        ksort($queryParams);

        $queryString = urldecode(http_build_query($queryParams));

        $fullUrl = "$url?$queryString";

        if(Cache::has($fullUrl)){
            return Cache::get($fullUrl);
        }else{
            // 產生查詢建構器
            $books_query = Book::query();

            /* ----------------------篩選條件 filters 參數----------------------- */

            $books_query->when(!is_null(request('filters')), function ($q) {
                
                // 把filters 中的 @XX@ 分割 並 轉為 collect型態
                $filters = collect(explode('@XX@', request('filters')));

                // 類似 foreach 將每筆 filter 讀出
                $filters->map(function($filter) use($q){
                    
                    // 分割filter 的 key value
                    list($key,$value) = explode(':', $filter);

                    // 查詢建構器 加上 where key value 條件
                    $q->where($key, 'like', "%$value%");
                });
            });

            /* ----------------------排列順序 filters 參數----------------------- */

            $books_query->when(!is_null(request('sorts')), function ($q) {
                
                $sorts = collect(explode('@XX@', request('sorts')));

                $sorts->map(function($sort) use($q){
                    
                    list($key,$value) = explode(':', $sort);

                    if(in_array($value, ['asc','desc','ASC','DESC'])){
                        $q->orderBy($key, $value);
                    }
                });
            });

            // 如果有分頁的筆數 再加上 否則預設抓所有
            $books = request('limit') ? $books_query->paginate(request('limit'))->appends($queryParams) : $books_query->get();
            
            return Cache::remember($fullUrl,60,function() use ($books){
                return response($books, Response::HTTP_OK);
            });
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $this->validate($request,[
            'ISBN' => 'required',
            'name' => 'required',
            'description' => 'required',
            'publisher_id' => 'required',
            'publish_date' =>'required|date',
            'author_id'=> 'required',
            'book_classification' => 'required'
        ]);


        if(!Book::where('ISBN',$request->ISBN)->exists()){
            $book = Book::create($request->all());
            $book = $book->refresh();
            return response($book, Response::HTTP_CREATED);
        }else{
            return response('The ISBN Number Book Already Exist', Response::HTTP_CONFLICT);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        return response($book,Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        $this->validate($request,[
            'publish_date' =>'date',
        ]);

        $book->update($request->all());
        return response($book,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        // Model 有加上softDelete 所以 這邊會進行軟刪除
        $book->delete();

        // 如果要強制刪除 就用forceDelete
        // $book->forceDelete();
        return response(null,Response::HTTP_NO_CONTENT);
    }
}
