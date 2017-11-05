<?php

namespace App\Http\Controllers;

use App\PastPaper;

class PastPaperController extends Controller {
	public function index() {
		return view( 'view_past_papers' );
	}

	public function create() {
		return view( 'upload_resources' );
	}


	public function getAll() {
		return PastPaper::paginate( 20 );
	}
}
