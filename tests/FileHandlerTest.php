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

        $this->visit('api/v1/types?all=true')->assertResponseStatus(200);

        $this->visit('api/v1/files?all=true')->assertResponseStatus(200)->seeJsonStructure(['results']);


        $this->json("POST",'api/v1/files',
            ["file" => $file ])
            ->assertResponseStatus(400);

	    $file = new UploadedFile( storage_path( 'testing/file.pdf' ), 'file.pdf', null, filesize( storage_path( 'testing/file.pdf' ) ), null, true );
	    $this->json( "POST", 'api/v1/files',
		    [ "file" => $file ] )
	         ->assertResponseStatus( 200 );
	    $data = json_decode( $this->response->content() );
	    $this->json( 'GET', 'file/type/details/' . $data->filetype )
	         ->seeJsonStructure( [ 'checksum' ] )
	         ->seeJson( [ 'filetype' => 'pdf' ] );
	    $this->call( 'GET', 'file/type/' . $data->filetype );
	    $this->assertTrue( $this->response->headers->get( 'content-type' ) == 'application/pdf' );
	    $this->json( 'GET', 'file/name/details/' . $data->file_name )
	         ->seeJsonStructure( [ 'checksum' ] )
	         ->seeJson( [ 'filetype' => 'pdf' ] );
	    $this->call( 'GET', 'file/name/' . $data->file_name );
	    echo $this->response->getStatusCode();
	    $this->assertTrue( $this->response->headers->get( 'content-type' ) == 'application/pdf' );
    }
}
