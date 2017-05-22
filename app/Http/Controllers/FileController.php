<?php

namespace App\Http\Controllers;

use App\File;
use App\Type;
use App\Utilities\FileUtilities;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class FileController extends Controller
{
    //Retrieve File
    public function retrieveFile($type)
    {

        $fileDetails = Type::where('name', $type)->first();


        if (count($fileDetails) < 1) {

            $data = ["status_code" => 404, 'error' => "$type not found"];
        } else {
            $data = FileUtilities::getFile($fileDetails->id);
        }

        $data = $this->downloadFile($data);

        return $data;

    }

    public function saveFileType(Request $request)
    {
        $newFileTypeData = $request->all();

        $type = $this->createType($newFileTypeData);

        return $type;
    }

    public function saveFile(Request $request)
    {
        //Validate requests
        $this->validate($request, [
            'file' => 'required',
        ]);

        $resource = $request->file('file');

        $filetype = $request->file('file')->getClientOriginalExtension();

        $originalfilename = $request->file('file')->getClientOriginalName();
        //Append Unique Identifier File Name
        $datenow = self::fileCreationDate();
        //Remove Special Characters
        $tmpfilename = str_replace(' ', '_', $originalfilename);
        //Concatenate filename and date
        $filename = $datenow . '_' . $tmpfilename;
        //Store File
        FileUtilities::storeFile($resource, $filename);

        //Check File Type Exists and Create if Does'nt Create a File Type.
        $paramType = Type::firstOrCreate(['name' => $filetype]);
        //Store File Details
        $file = $this->storeFileMetadata($filename, $paramType);

        return $file;
    }

    private static function fileCreationDate()
    {
        $datenow = \Carbon\Carbon::now()->toDateTimeString();
        $datenow = str_replace(':', '_', $datenow);
        $datenow = str_replace('-', '_', $datenow);
        return $datenow;
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

    /**
     * @param $data
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function downloadFile($data)
    {
        if ($data != null && !isset($data['status_code'])) {

            $data = response()->download($data);
        }
        return $data;
    }

}
