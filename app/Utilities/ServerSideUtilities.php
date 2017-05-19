<?php


namespace App\Utilities;


class ServerSideUtilities
{
    public static function getBasicServerSideData($draw,$url)
    {


        $unfilteredData = (ApiUtilities::IssueGETRequest($url));


        $apiResponseArray = json_decode($unfilteredData, true);

        $apiResponseItemCount = $apiResponseArray['iTotal'];
        $apiResponsePageCount = $apiResponseArray['iFilteredTotal'];
        $apiResponseData = $apiResponseArray['results'];

        $serverSideData = collect([]);
        $serverSideData->put('draw', (int)$draw);
        $serverSideData->put('recordsTotal', $apiResponseItemCount);
        $serverSideData->put('recordsFiltered', $apiResponsePageCount);

        $serverSideData->put('data', $apiResponseData);

        return $serverSideData;

    }

}