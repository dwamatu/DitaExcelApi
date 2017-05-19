<?php

namespace App\Http\Controllers;


use App\File;
use App\Utilities\FileUtilities;
use Schema;
use App\Type;
use App\Utilities\FunctionsUtilities;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use Mockery\Exception;


class BaseController extends Controller
{
    protected $response = array();


    //<editor-fold desc="Issue Get Request">
    //
    public function issueGetRequest(Request $request, $table, $id = null, $q = null)
    {

        $pageSize = $request->query('pageSize');
        $offSet = $request->query('offSet');
        $all = $request->query('all');
        $q = $request->query('q');


        if (Schema::hasTable($table)) {
            if ($id != null) {
                $data = self::fetchOne($table, $id);
                if (isset($data['error'])) {
                    $this->response['errors'] = $data;
                } else {

                    $this->response['resource'] = $data;
                }
            } else {
                $this->response = FunctionsUtilities::fetchList($table, $pageSize, $offSet, $all, $q);

            }
            return $this->response;
            //
        } else {
            $this->response['errors'] = ['status_code' => 404, "error" => "resource $table does not exits"];
        }
        return $this->response;

    }

    public static function fetchOne($resource, $id)
    {
        $models = [
            'type' => Type::class,
            'files' => File::class,

        ];

        try {
            $data = $models[$resource]::findOrFail($id);
        } catch (ModelNotFoundException $e) {

            $data = ["status_code" => 404, 'error' => "$resource not found"];
        }

        return $data;

    }

    //</editor-fold>

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
