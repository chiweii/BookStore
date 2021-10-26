<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Symfony\Component\HttpFoundation\Response;

use App\Http\Resources\BookResource;
use App\Http\Resources\BookCollection;

class BookController extends Controller
{
    public function __construct(){

        //客戶端 只有 有scope create-books 權限 的才能夠 store
        $this->middleware('scopes:create-books',['only'=>['store']]); 

        //客戶端 除了 書籍資料列表 跟 單一書籍資料，其餘都要有身分登入
        $this->middleware('auth:api',['except' => ['index','show']]); 

        //客戶端 僅能夠查看 書籍資料列表 跟 單一書籍資料
        $this->middleware('client',['only'=>['index','show']]); 
    }
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
            // 產生查詢建構器，並用預處理with方法讀取關聯type資料表
            $books_query = Book::query()->with('type');

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
                return new BookCollection($books);
                // return response($books, Response::HTTP_OK);
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
        $this->authorize('create',Book::class);
        
        $this->validate($request,[
            'ISBN' => 'required',
            'name' => 'required',
            'description' => 'required',
            'publisher_id' => 'required',
            'publish_date' =>'required|date',
            'author_id'=> 'required',
            'type_id' => 'required|exists:types,id',
            'book_classification' => 'required'
        ]);


        if(!Book::where('ISBN',$request->ISBN)->exists()){

            try {
                DB::beginTransaction();

                $book = Book::create($request->all());
                $book->load('type');

                $book->likes()->attach(auth()->user()->id);

                DB::commit();

                return new BookResource($book);
            } catch (Exception $e) {
                
                DB::rollback();

                $errorMessage = 'MESSAGE : '.$e->getMessage();
                Log::error($errorMessage);

                return response(
                    ['error' => '程式異常'], 
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
            // $book = Book::create($request->all());
            // $book->load('type');
            // return new BookResource($book);
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
        $book->load('type');
        return new BookResource($book);
        // dd($book);
        // return response($book,Response::HTTP_OK);
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
        $this->authorize('update',$book);

        $this->validate($request,[
            'publish_date' =>'date',
        ]);

        $book->update($request->all());

        $book->load('type');
        return new BookResource($book);
        // return response($book,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        $this->authorize('delete',$book);

        // Model 有加上softDelete 所以 這邊會進行軟刪除
        $book->delete();

        // 如果要強制刪除 就用forceDelete
        // $book->forceDelete();
        return response(null,Response::HTTP_NO_CONTENT);
    }
}
