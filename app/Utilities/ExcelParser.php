<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 15/07/17
 * Time: 22:45
 */

namespace App\Utilities;


use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Readers\LaravelExcelReader;

// TODO: add support for all timetable variations
// TODO: finidh copyToDatabase and finish tests

class ExcelParser
{
    STATIC $i = 0;
    STATIC $j = 0;

    public static function copyToDatabase($path)
    {
        Excel::load($path, function (LaravelExcelReader $reader) {
            $reader->noHeading();
            $reader->each(function ($sheet) {
                $title = $sheet->getTitle();
                ExcelParser::$i = 0;
                $sheet->each(function ($row) {
                    ExcelParser::$j = 0;
                    $row->each(function ($cell) {
                        $matches = array();
                        $sections = ExcelParser::split($cell);
                        $pattern = '/' . $sections[0] . '(?:-|\s|.{0})' . $sections[1] . '/i';
                        //echo ExcelParser::$i . ' ' . ExcelParser::$j;
                        if (preg_match($pattern, $cell, $matches) == 1) {
                            $details = self::getDetails($sheet);
                            $details = self::stringToDate($details);
                            $details->subHours(2); // all exams have a duration of two hours
                        }
                        ExcelParser::$j++;
                    });
                    ExcelParser::$i++;
                });
            });
            //Log::info('TOTAL SHEETS: ' . $total);
        });
    }

    public static function split($text)
    {
        if (strpos($text, '-') !== false) {
            return explode("-", $text);
        } else if (strpos($text, ' ') !== false) {
            return explode(" ", $text);
        } else {
            return array(substr($text, 0, 3), substr($text, 3));
        }
    }

    public static function getDetails(&$sheet)
    {
        $row = ExcelParser::$i;
        $col = ExcelParser::$j;

        $match = array();
        $pattern = '/(?:-([\d]+:[\d]+[apm]+))|(?:-([\d]+\\.[\d]+[apm]+))/i';
        for ($i = $row; $i >= 0; $i--) {
            $cell = $sheet->get($i)->get($col);

            if (preg_match($pattern, $cell, $match) == 1) {
                $date = self::getDate($sheet, $i);

                if ($date != null) {
                    return $date . ' ' . strtolower($match[1]);
                }
            }
        }

        return null;
    }

    public static function getDate(&$sheet, $row)
    {
        $row--;
        $col = ExcelParser::$j;
        $match = array();
        $pattern = '/[\w]+day[\s]+([\d]+\/[\d]+\/[\d]+)/i';

        for ($j = $col; $j >= 0; $j--) {
            $cell = $sheet->get($row)->get($j);
            if (preg_match($pattern, $cell, $match) == 1) {
                return $match[1];
            }
        }

        return null;
    }

    public static function stringToDate($string)
    {
        $dateTime = Carbon::parse(date_format($string, 'd/m/y g:ia'));
        return $dateTime;
    }
}