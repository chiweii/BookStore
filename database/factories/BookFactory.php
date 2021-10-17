<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Type;
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
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'publisher_id' => $this->faker->randomNumber(5, false),
            'publish_date' => $this->faker->date(),
            'author_id' => $this->faker->randomNumber(5, false),
            'type_id' => Type::all()->random()->id,
            'shelf' => $this->faker->boolean,
            'book_classification' => '1,2,3',
        ];
    }
}
