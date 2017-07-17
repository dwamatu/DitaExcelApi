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
        $dateTimeDetails = self::getDateTimeDetails($sheet);
        $dateTimeDetails = self::stringToDate($dateTimeDetails);
        $dateTimeDetails->subHours(2); // all exams have a duration of two hours
        $shift = self::getShift($sheet->getTitle());
        $room = $sheet->get(ExcelParser::$i)->get(0);
        $details = [
            'dateTime' => $dateTimeDetails,
            'shift' => $shift,
            'room' => $room
        ];

        return $details;

    }

    public static function getDateTimeDetails(&$sheet)
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
        $dateTime = \DateTime::createFromFormat('d/m/y g:ia', $string);
        return Carbon::createFromTimestamp($dateTime->getTimestamp());
    }

    public static function getShift($string)
    {
        $string = strtolower($string);
        if (strpos($string, 'athi') !== false) {
            return 'athi';
        } elseif (strpos($string, 'evening') !== false) {
            return 'evening';
        } else {
            return 'day';
        }
    }

    public static function sanitize($string)
    {
        // remove any whitespaces
        $string = preg_replace('/\s/', '', $string);
        if (strpos($string, '/') != false) {
            $course_codes = array();
            if (preg_match('/[a-z]{3}[\d]{3}[a-z]{1}\/[a-z]{1}(?:[\/]*|.{})/i', $string) == 1) { // handle type YYY111A/B
                $prefix = substr($string, 0, 6);
                $sections = explode('/', substr($string, 6));
                foreach ($sections as $section) {
                    array_push($course_codes, $prefix . $section);
                }
            } else if (preg_match('/[a-z]{3}[\d]{3}[a-z]{1}(?:\/|.{0})/i', $string) == 1) { // handle type YYY111A/YYY222A
                $course_codes = explode('/', $string);
            }

            return $course_codes;

        } else {
            return array($string);
        }


    }


}