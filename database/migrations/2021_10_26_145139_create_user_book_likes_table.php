<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBookLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_book_likes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('book_id')->unsigned()->comment('書籍ID');
            $table->bigInteger('user_id')->unsigned()->comment('使用者ID');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_book_likes', function (Blueprint $table) {
            $table->dropForeign('user_book_likes_user_id_foreign');
            $table->dropForeign('user_book_likes_book_id_foreign');
        });
        Schema::dropIfExists('user_book_likes');
    }
}
