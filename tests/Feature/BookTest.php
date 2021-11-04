<?php

namespace Tests\Feature;
use App\Models\Type;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;
    
    public function testViewAllBook(){

        Passport::actingAsClient(
          Client::factory()->create()  
        );

        Type::factory(5)->create();

        User::factory(5)->create();

        Book::factory(20)->create();

        $response = $this->json('GET','api/v1/books?&limit=10');

        $this->withExceptionHandling();

        $resultStructure = [
            "data" => [
                '*' => [
                    "id","ISBN","name","description","publisher_id","publish_date",
                    "publish_age","author_id","type_id","type_name","book_classification",
                    "created_at","updated_at"
                ]
            ],
            "links" => [
                "first","last","prev","next"
            ],
            "meta" => [
                "current_page","from","last_page","path","per_page","to","total"
            ]
        ];

        $response->assertStatus(200)->assertJsonStructure($resultStructure);
    }

    public function testCanCreateBook(){

        $user = User::factory()->create();

        Passport::actingAs(
            $user,['create-books']
        );

        $this->withoutExceptionHandling();

        $type = Type::factory()->create();

        $formData = [
            "ISBN" => 95412134623,
            "name" => '測試的書籍',
            "description" => 'testt',
            "publisher_id" => 1,
            "publish_date" => '2021-05-27',
            "author_id" => 1,
            "type_id" => 1,
            "book_classification" => '1,3,6'
        ];

        $response = $this->json(
            'POST',
            'api/v1/books',
            $formData
        );

        $response->assertStatus(201)->assertJson([
            'data' => $formData,
        ]);
    }
    
    public function testCanNotCreateBook(){

        $type = Type::factory()->create();
        
        $formData = [
            "ISBN" => 95412134623,
            "name" => '測試的書籍',
            "description" => 'testt',
            "publisher_id" => 1,
            "publish_date" => '2021-05-27',
            "author_id" => 1,
            "type_id" => 1,
            "book_classification" => '1,3,6'
        ];

        $response = $this->json(
            'POST',
            'api/v1/books',
            $formData
        );

        $response->assertStatus(401)->assertJson([
            "message" => "Unauthenticated."
        ]);
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // public function test_example()
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }
}
