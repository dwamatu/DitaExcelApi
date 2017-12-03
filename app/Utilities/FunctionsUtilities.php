<?php

namespace App\Utilities;


use DB;

class FunctionsUtilities
{


    public static function fetchList($table, $pageSize = null, $offset = null, $all = null,$q=null)
    {

        $responceCollection = collect([]);

        $iTotal = DB::table($table)->count();
        if (isset($all)) {
            $results = DB::table($table)->get();
        } else if (isset($pageSize) && !empty($offset)) {

            $results = DB::table($table)->skip($offset)->take($pageSize)->get();
        }else if(isset($q))
        {
            $results = DB::table($table)->where()->get();
        } else {
            $results = DB::table($table)->skip(0)->take(10)->get();

        }

        $iFilteredTotal = count($results);


        $responceCollection->put('iTotal', $iTotal);
        $responceCollection->put('iFilteredTotal', $iTotal);
        $responceCollection->put('results', $results);


        return $responceCollection;


    }

}