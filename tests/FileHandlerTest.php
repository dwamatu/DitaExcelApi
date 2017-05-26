<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;

class FileHandlerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    use WithoutMiddleware;
    use DatabaseTransactions;

    public function testExample()
    {
        $faker = \Faker\Factory::create();

        $file = new UploadedFile(storage_path('testing/image.jpg'), 'image.jpg', filesize(storage_path('testing/image.jpg')), 'image/jpg', null, true);

        $this->visit('api/v1/types')->assertResponseStatus(200);
        $this->json("POST",'api/v1/types',['name'=>$faker->text(5)])->assertResponseStatus(200)->seeJsonStructure(['id']);

        $this->visit('api/v1/files')->assertResponseStatus(200)->seeJsonStructure(['results']);


        $this->json("POST",'api/v1/files',
            ["file" => $file ])
            ->assertResponseStatus(200)
            ->seeJsonStructure(['id']);

        $this->call('GET', 'file/jpg');
        $this->assertTrue($this->response->headers->get('content-type') == 'image/jpeg');

    }
}
