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

    public function testApi()
    {
        $faker = \Faker\Factory::create();

        $file = new UploadedFile(storage_path('testing/image.jpg'), 'image.jpg', null, filesize(storage_path('testing/image.jpg')), null, true);

        $this->visit('api/v1/types')->assertResponseStatus(200);
        $this->json("POST",'api/v1/types',['name'=>$faker->text(5)])->assertResponseStatus(200)->seeJsonStructure(['id']);

        $this->visit('api/v1/files')->assertResponseStatus(200)->seeJsonStructure(['results']);


        $this->json("POST",'api/v1/files',
            ["file" => $file ])
            ->assertResponseStatus(200)
            ->seeJsonStructure(['id'])
            ->seeJson(['name' => 'jpg']);
        $data = json_decode($this->response->content());
        $this->json('GET', 'file/details/' . $data->type->name)
            ->seeJsonStructure(['checksum'])
            ->seeJson(['name' => 'jpg']);
        $this->call('GET', 'file/' . $data->type->name);
        $this->assertTrue($this->response->headers->get('content-type') == 'image/jpeg');
    }

    public function testExcelConversion()
    {
        $file = new UploadedFile(storage_path('testing/excel-new.xlsx'), 'excel-new.xlsx', null, filesize(storage_path('testing/excel-new.xlsx')), null, true);
        $this->json("POST", 'api/v1/files',
            ["file" => $file])
            ->assertResponseStatus(200)
            ->seeJsonStructure(['id'])
            ->seeJsonStructure(['checksum'])
            ->seeJson(['name' => 'xls']);
        $data = json_decode($this->response->content());
        $this->json('GET', 'file/details/' . $data->type->name)
            ->seeJsonStructure(['checksum'])
            ->seeJson(['name' => 'xls']);
        $this->call('GET', 'file/xls');
        $this->assertTrue($this->response->headers->get('content-type') == 'application/vnd.ms-office');
    }
}
