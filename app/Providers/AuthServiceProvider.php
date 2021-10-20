<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        
        //設定access_token 10天過期
        Passport::tokensExpireIn(now()->addDays(10));

        //設定refresh_token 20天過期
        Passport::refreshTokensExpireIn(now()->addDays(20));

        Passport::tokensCan([
            'create-books' => '新增 書籍資料',
            'update-books' => '更新 書籍資料',
            'delete-books' => '刪除 書籍資料',
            'read-books' => '取得 書籍資料',
            'CRUD-books' => '新增、取得、更新、刪除 書籍資料',
            'create-types' => '新增 分類資料',
            'update-types' => '更新 分類資料',
            'delete-types' => '刪除 分類資料',
            'read-types' => '取得 分類資料',
            'CRUD-types' => '新增、取得、更新、刪除 分類資料',
            'user-info' => '登入會員資料'
        ]);
    }
}
