<?php

namespace App\Http\Controllers;

use App\File;
use App\Type;
use App\Utilities\FileUtilities;
use Illuminate\Http\Request;

class FileController extends Controller
{
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
        $filename = $request->file('file')->getClientOriginalName();
        //Store File
        FileUtilities::storeFile($resource,$filename);

        //Check File Type Exists and Create if Does'nt Create a File Type.
        $paramType = Type::firstOrCreate(['name' => $filetype] );
        //Store File Details
        $file = $this->storeFileMetadata($filename, $paramType);

        return $file;
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

}
