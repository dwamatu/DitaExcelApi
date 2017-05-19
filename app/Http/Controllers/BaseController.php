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



}
