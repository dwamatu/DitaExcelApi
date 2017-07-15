<?php

namespace App\Http\Controllers;

use App\File;
use App\Type;
use App\Utilities\ExcelParser;
use App\Utilities\FileUtilities;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FileController extends Controller
{
    //Retrieve File
    public function retrieveFile($type)
    {

        $fileDetails = Type::latest()->where('name', $type)->firstOrFail();
        if (env('APP_ENV', 'local') == 'production') {
            $data = FileUtilities::getFileCloud($fileDetails->id);
        } else {
            $data = FileUtilities::getFile($fileDetails->id);
        }

        return $this->downloadFile($data);
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

    public function retrieveFileDetails($type)
    {
        $fileDetails = Type::latest()->where('name', $type)->firstOrFail();
        $data = FileUtilities::getDetails($fileDetails->id);
        $data->load(['type' => function ($query) {
            $query->select('name', 'file_id');
        }]);
        return $data->toJson();
    }

    public function saveFileType(Request $request)
    {
        $newFileTypeData = $request->all();

        $type = $this->createType($newFileTypeData);

        return $type;
    }

    /**
     * @param $newFileTypeData
     * @return Type
     */
    private function createType($newFileTypeData)
    {
        $type = new Type();
        $type->name = $newFileTypeData['name'];
        $type->save();

        return $type;
    }

    public function saveFile(Request $request)
    {
        //Validate requests
        $this->validate($request, [
            'file' => 'required|max:8000',
        ]);

        $ext = $request->file('file')->getClientOriginalExtension();

        if ($ext != 'xls' && $ext != 'xlsx') {
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
        $file = new File();
        $file->file_name = $filename;
        $file->checksum = $checksum;
        $file->save();
        $file->type()->create(['name' => $fileType]);
        $file->load(['type' => function ($query) {
            $query->select('name', 'file_id');
        }]);
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

        ExcelParser::copyToDatabase($resource->getRealPath());
        return response()->json('Saved successfully');
    }

    /**
     * @param $filename
     * @param $paramType
     * @return File
     */
    private function storeFileMetadata($filename, $paramType)
    {
        $file = new File();

        $file->file_name = $filename;
        $file->file_type = $paramType->id;

        $file->save();
        return $file;
    }

}
