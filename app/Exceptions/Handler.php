<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;

use Symfony\Component\HttpFoundation\Response;

use App\Traits\ApiResponseTrait; //引用Api回復的特徵

use Throwable;

class Handler extends ExceptionHandler
{

    use ApiResponseTrait; //使用特徵，將Trait撰寫的方法貼到這個類別中使用


    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception){

        // 判斷要求的資料回傳格式是不是 Json
        if($request->expectsJson()){

            // 1. Model 找不到資源
            if($exception instanceof ModelNotFoundException){
                return $this->errorResponse(
                    '找不到資源',
                    Response::HTTP_NOT_FOUND
                );
            }

            // 2. 網址輸入錯誤 ( 新增判斷 )
            if($exception instanceof NotFoundHttpException){
                return $this->errorResponse(
                    '無效的網址',
                    Response::HTTP_NOT_FOUND
                );
            }

            // 3. 網址不接受此請求動作 ( 新增判斷 )
            if($exception instanceof MethodNotAllowedHttpException){
                return $this->errorResponse(
                    $exception->getMessage(), //回傳例外的訊息
                    Response::HTTP_NOT_FOUND
                );
            }

            // 4. API AUTH SCOPE 權限不符合
            // dd($exception);
            if($exception instanceof AuthorizationException){
                return $this->errorResponse(
                    '存取權限不足，請確認您身分的權限範圍', //回傳例外的訊息
                    Response::HTTP_FORBIDDEN
                );
            }            
        }
        return parent::render($request, $exception);
    }

    public function unauthenticated($request, AuthenticationException $exception){
        // client要求json格式的才回饋正確
        if($request->expectsJson()){
            return $this->errorResponse(
                $exception->getMessage(),
                Response::HTTP_UNAUTHORIZED
            );
        }else{
            // 非client 請求json 轉回登入畫面
            return redirect()->guest($exception->redirectTo() ?? route('login'));
        }
    }
}
