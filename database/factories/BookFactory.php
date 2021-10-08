<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'ISBN' => $this->faker->unique()->isbn13(),
            'name' => $this->faker->word(),
            'description' => $this->faker->text,
            'publisher_id' => $this->faker->randomNumber(5, false),
            'publish_date' => $this->faker->date(),
            'author_id' => $this->faker->randomNumber(5, false),
            'shelf' => $this->faker->boolean,
            'book_classification' => '1,2,3',
        ];
    }
}
