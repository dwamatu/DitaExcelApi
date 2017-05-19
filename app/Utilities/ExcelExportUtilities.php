<?php

namespace App\Utilities;


use Maatwebsite\Excel\Facades\Excel;

class ExcelExportUtilities
{

    public static function downloadExportExcel($file, $data, $sheetName = null, $id = null)
    {
        $documentType = ['xls','csv'];
        $exportFileName = self::createFileName($file, $sheetName);
        //Specify Document between CSV and Excel
        if ($id === null) {
            $type = 'xls';
        } else
        {
            $type = $documentType[$id] ;
        }
        //Create Excel
        Excel::create($exportFileName['filename'], function ($excel) use ($exportFileName, $data) {
            $excel->sheet($exportFileName['title'], function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });

        })->export($type);
    }

    //FILE CREATION DATE
    private static function fileCreationDate()
    {
        $datenow = \Carbon\Carbon::now()->toDateTimeString();
        $datenow = str_replace(':', '_', $datenow);
        $datenow = str_replace('-', '_', $datenow);
        return $datenow;
    }

    /**
     * @param $file
     * @return string
     */
    private static function createFileName($file, $sheetName)
    {   //Get Date & Time
        $datenow = self::fileCreationDate();

        //Remove Special Characters
        $tmpfilename = str_replace(' ', '_', $file);
        //Concatenate filename and date
        $filename = $tmpfilename .'_'. $datenow;
        $filenamedata = collect();
        $filenamedata->put('filename', $filename);

        if ($sheetName === null) {
            $sheetName = 'Sheet 1';
        }

        $filenamedata->put('title', $sheetName);

        return $filenamedata;
    }

}