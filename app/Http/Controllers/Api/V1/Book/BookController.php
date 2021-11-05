<?php

namespace App\Http\Controllers\Api\V1\Book;
use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Symfony\Component\HttpFoundation\Response;

use App\Http\Resources\BookResource;
use App\Http\Resources\BookCollection;

use App\Http\Requests\StoreBookRequest;
use App\Services\BookService;

class BookController extends Controller
{
    public function __construct(BookService $bookService){

        //客戶端 只有 有scope create-books 權限 的才能夠 store
        $this->middleware('scopes:create-books',['only'=>['store']]); 

        //客戶端 除了 書籍資料列表 跟 單一書籍資料，其餘都要有身分登入
        $this->middleware('auth:api',['except' => ['index','show']]); 

        //客戶端 僅能夠查看 書籍資料列表 跟 單一書籍資料
        // $this->middleware('client',['only'=>['index','show']]); 

        $this->bookService = $bookService;
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
            $books = $this->bookService->getListData(request(), $queryParams);
            
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
    public function store(StoreBookRequest $request)
    {   
        $this->authorize('create',Book::class);

        if(!Book::where('ISBN',$request->ISBN)->exists()){

            try {
                DB::beginTransaction();

                $book = Book::create($request->all());
                $book->load('type');

                $book->likes()->attach(auth()->user()->id);

                DB::commit();

                return new BookResource($book);
            } catch (\Exception $e) {
                
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
    * @OA\Delete(
    *    path="/api/V1/books/{id}",
    *    operationId="bookDelete",
    *    tags={"Book"},
    *    summary="刪除書籍資料",
    *    description="刪除書籍資料",
    *    @OA\Parameter(
    *    　　name="id",
    *    　　description="Book id",
    *    　　required=true,
    *    　　in="path",
    *    　　@OA\Schema(
    *      　　type="integer"
    *    　　)
    *　　),
    * 　　security={
    * 　　   {
    * 　　     "passport": {}
    * 　　   }
    * 　　},
    *　　 @OA\Response(
    *　　    response=204,
    * 　　   description="刪除成功回傳空值"
    * 　　),
    * 　　@OA\Response(
    * 　　   response=404,
    * 　　   description="找不到資源"
    * 　　)
    * )
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
