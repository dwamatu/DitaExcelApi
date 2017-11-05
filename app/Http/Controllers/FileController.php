<?php

namespace App\Http\Controllers;

use App\File;
use App\Unit;
use App\Utilities\ExcelParser;
use App\Utilities\FileUtilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class FileController extends Controller
{
	public function retrieveFile( $fetch_type, $identifier ) {
		switch ( $fetch_type ) {
			case 'type':
				return $this->retrieveFileByType( $identifier );
				break;
			case 'name':
				return $this->retrieveFileByName( $identifier );
				break;
			default:
				return $this->verifyData( null );
		}
	}

    //Retrieve File
	public function retrieveFileByType( $type )
    {
        if (env('APP_ENV', 'local') == 'production') {
	        $data = FileUtilities::getFileCloudByType( $type );
        } else {
	        $data = FileUtilities::getFileByType( $type );
        }

	    $this->verifyData( $data );

        return $this->downloadFile($data);
    }

	public function retrieveFileByName( $name ) {
		if ( env( 'APP_ENV', 'local' ) == 'production' ) {
			$data = FileUtilities::getFileCloudByName( $name );
		} else {
			$data = FileUtilities::getFileByName( $name );
		}

		$this->verifyData( $data );

		return $this->downloadFile( $data );
	}

    /**
     * @param $data
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function downloadFile($data)
    {
        if ($data != null && !isset($data['status_code'])) {

            $data = response()->download($data)->deleteFileAfterSend(true);;
        }
        return $data;
    }

	public function retrieveFileDetails( $fetch_type, $identifier ) {
		$response = null;
		switch ( $fetch_type ) {
			case 'type':
				$data     = FileUtilities::getDetailsByType( $identifier );
				$response = $data->toJson();
				break;
			case 'name':
				$data     = FileUtilities::getDetailsByName( $identifier );
				$response = $data->toJson();
				break;
			default:
				$response = response()->json( 'File not found', 404 );
		}

		return $response;
    }

    public function saveFile(Request $request)
    {
        //Validate requests
        $this->validate($request, [
            'file' => 'required|max:8000',
        ]);

        $ext = $request->file('file')->getClientOriginalExtension();

	    if ( $ext != 'xls' && $ext != 'xlsx' && $ext != 'pdf' ) {
            return response()->json('Bad request (Invalid file)', 400);
        }

        $resource = $request->file('file');
        $checksum = hash_file('md5', $resource->getRealPath());;

        $fileType = $request->file('file')->getClientOriginalExtension();
        $originalFilename = $request->file('file')->getClientOriginalName();
        if ($fileType === 'xlsx') {
            $result = Excel::load($request->file('file')->getRealPath())->store('xls', false, true);
            $path = $result['full'];
            $fileType = $result['ext'];
            $resource = new \Symfony\Component\HttpFoundation\File\File($path);
            $originalFilename = $resource->getFilename();
            $checksum = hash_file('md5', $resource->getRealPath());
        }
        //Append Unique Identifier File Name
        $now = self::fileCreationDate();
        //Remove Special Characters
        $tmpFilename = str_replace(' ', '_', $originalFilename);
        //Concatenate filename and date
        $filename = $now . '_' . $tmpFilename;
        //Store File
        if (env('APP_ENV', 'local') == 'production') {
            FileUtilities::storeFileCloud($filename, \Illuminate\Support\Facades\File::get($resource->getRealPath()));
        } else {
            FileUtilities::storeFile($resource, $filename);
        }


        //Check File Type Exists and Create if Does'nt Create a File Type.
        //$paramType = Type::firstOrCreate(['name' => $fileType]);
        //Store File Details
        //$file = $this->storeFileMetadata($filename, $paramType);
        $file = $this->storeFileMetadataWithChecksum($filename, $fileType, $checksum);

        return $file;
    }

    private static function fileCreationDate()
    {
        $now = \Carbon\Carbon::now()->toDateTimeString();
        $now = str_replace(':', '_', $now);
        $now = str_replace('-', '_', $now);
        return $now;
    }

    private function storeFileMetadataWithChecksum($filename, $fileType, $checksum)
    {
	    $file = new File( [
		    'file_name' => $filename,
		    'checksum'  => $checksum,
		    'filetype'  => $fileType
	    ] );
        $file->save();
        return $file;
    }

    public function saveFileToDB(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|max:8000',
        ]);

        $ext = $request->file('file')->getClientOriginalExtension();

        if ($ext != 'xls' && $ext != 'xlsx') {
            return response()->json('Bad request (Invalid file)', 400);
        }

        $resource = $request->file('file');
        Unit::truncate();
        Log::info('Saving to DB');
        ExcelParser::copyToDatabase($resource->getRealPath());
        Log::info('Done');
        return response()->json('Saved successfully');
    }

	private function verifyData( $data ) {
		if ( $data == null ) {
			abort( 404, 'File not found' );
		}
    }

}
