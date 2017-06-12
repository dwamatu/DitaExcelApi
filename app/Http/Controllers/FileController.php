<?php

namespace App\Http\Controllers;

use App\File;
use App\Type;
use App\Utilities\FileUtilities;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FileController extends Controller
{
    //Retrieve File
    public function retrieveFile($type)
    {

        $fileDetails = Type::where('name', $type)->firstOrFail();
        $data = FileUtilities::getFile($fileDetails->id);
        return $this->downloadFile($data);
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

    public function retrieveFileDetails($id)
    {
        $fileDetails = File::with(['type' => function ($query) {
            $query->select('name', 'file_id');
        }])->first();
        $data = $fileDetails->toJson();
        return $data;
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
            'file' => 'required',
        ]);

        $resource = $request->file('file');
        $checksum = hash_file('md5', $resource->getRealPath());;

        $fileType = $request->file('file')->getClientOriginalExtension();
        if ($fileType === 'xlsx') {
            $result = Excel::load($request->file('file')->getRealPath())->store('xls', false, true);
            $path = $result['full'];
            $fileType = $result['ext'];
            $resource = new \Symfony\Component\HttpFoundation\File\File($path);
            $checksum = hash_file('md5', $resource->getRealPath());
        }
        $originalFilename = $request->file('file')->getClientOriginalName();
        //Append Unique Identifier File Name
        $now = self::fileCreationDate();
        //Remove Special Characters
        $tmpFilename = str_replace(' ', '_', $originalFilename);
        //Concatenate filename and date
        $filename = $now . '_' . $tmpFilename;
        //Store File
        FileUtilities::storeFile($resource, $filename);

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
