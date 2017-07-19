<?php

namespace App\Utilities;


use DB;

class FunctionsUtilities
{


    public static function fetchList($table, $pageSize = null, $offset = null, $all = false, $q = null, $where = false, $filters = null, $andWhere = null)
    {

        $responseCollection = collect([]);

        $iTotal = DB::table($table)->count();
        if (isset($all)) {
            $results = DB::table($table)->get();
        } else if ($where && !empty($filters)) {
            //Collection to hold everything
            $queryCollection = collect($filters);
            $query = DB::table($table);
            foreach ($queryCollection->keys() as $key) {
                $item = $queryCollection->get($key);
                if (is_array($item)) {
                    $i = 0;
                    foreach ($item as $subitem) {
                        if ($i == 0) {
                            $query->where($key, 'like', $subitem);
                        } else {
                            $query->orWhere($key, 'like', $subitem);
                        }
                        $i++;
                    }
                } else {
                    $query->where($key, $item);
                }
            }
            //$query->toSql() . "\n";
            $results = collect($query->get());
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