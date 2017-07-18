<?php

namespace App\Utilities;


use DB;

class FunctionsUtilities
{


    public static function fetchList($table, $pageSize = null, $offset = null, $all = null, $q = null, $where = null, $equals = null,$andWhere=null)
    {

        $responseCollection = collect([]);

        $iTotal = DB::table($table)->count();
        if (isset($all)) {
            $results = DB::table($table)->get();
        } else if (isset($where) && !empty($equals)) {
            //Collection to hold everything
            $responseDataCollection = collect();
            $queryCollection = collect(explode(',', $equals));
            $collection = $queryCollection->each(function ($item, $key) use ($responseDataCollection, $table,$where) {
                $units = DB::table($table)->where($where, $item)->get();
                if (!empty($units)) {
                    $responseDataCollection->push($units);
                }
            });
            $results = $responseDataCollection;
        } else if (isset($pageSize) && !empty($offset)) {

            $results = DB::table($table)->skip($offset)->take($pageSize)->get();
        } else if (isset($q)) {
            $results = DB::table($table)->where()->get();
        } else {
            $results = DB::table($table)->skip(0)->take(10)->get();

        }

        $iFilteredTotal = count($results);


        $responseCollection->put('iTotal', $iTotal);
        $responseCollection->put('iFilteredTotal', $iFilteredTotal);
        $responseCollection->put('results', $results);

        \Log::info('response',[$responseCollection]);

        return $responseCollection;


    }

}