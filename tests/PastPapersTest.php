<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 05/11/17
 * Time: 20:42
 */

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;

class PastPapersTest extends TestCase {

	use DatabaseTransactions;
	use WithoutMiddleware;

	public function testPastPapersModel() {
		$paper = factory( \App\PastPaper::class )->create();

		$this->assertDatabaseHas( 'past_papers', [
			'name'          => $paper->name,
			'resource_type' => $paper->resource_type,
			'semester'      => $paper->semester
		] );
	}

	public function testPastPapersRoutes() {
		$paper = factory( \App\PastPaper::class )->make();
		$file  = new UploadedFile( storage_path( 'testing/file.pdf' ), 'file.pdf', null, filesize( storage_path( 'testing/file.pdf' ) ), null, true );

		$this->json( "POST", 'api/v2/papers' )
		     ->assertStatus( 422 );
		$response = $this->json( "POST", 'api/v2/papers', [
			"name"          => $paper->name,
			"semester"      => $paper->semester,
			"resource_type" => $paper->resource_type,
			"file"          => $file,
		] )->assertSuccessful()
		     ->assertJsonFragment( [ 'File saved successfully' ] );

		$this->assertDatabaseHas( 'past_papers', [
			'name'          => $paper->name,
			'resource_type' => $paper->resource_type,
			'semester'      => $paper->semester
		] );

		$paper = \App\PastPaper::all()->first();
		$this->assertNotNull( $paper );
		$this->call( 'GET', 'file/name/' . $paper->file )
		     ->assertHeader( 'content-type', 'application/pdf' );
		$file = UploadedFile::fake()->create( 'bigfile.pdf', 9160 );
		$this->json( "POST", 'api/v2/papers', [
			"name"          => $paper->name,
			"semester"      => $paper->semester,
			"resource_type" => $paper->resource_type,
			"file"          => $file,
		] )->assertStatus( 422 )
		     ->assertJsonFragment( [ 'The max file size is 8196 kilobytes' ] );
	}
}