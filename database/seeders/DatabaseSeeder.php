<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

use App\Models\Book;
use App\Models\User;
use App\Models\Type;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Book::truncate();
        Type::truncate();

        Type::factory(10)->create();
        Book::factory(50)->create();
        Schema::enableForeignKeyConstraints();
    }
}
