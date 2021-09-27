<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('ISBN')->comment('ISBN書號');
            $table->string('name')->comment('書籍名稱');
            $table->longText('description')->comment('書籍簡介');
            $table->string('publisher_id',255)->comment('publisher.id');
            $table->date('publish_date')->comment('出版日期');
            $table->string('author_id',255)->comment('author.id');
            $table->boolean('shelf')->default(0)->comment('上架 (1:ON ; 0:OFF)');
            $table->text('book_classification')->comment('多個 book_class.id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
}
