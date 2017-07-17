<?php
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Readers\LaravelExcelReader;

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 15/07/17
 * Time: 23:17
 */
class ParserTest extends TestCase
{
    public function testSplit()
    {
        $string1 = 'ACS-113';
        $string2 = 'ACS 113';
        $string3 = 'ACS113';

        $result = \App\Utilities\ExcelParser::split($string1);
        $this->assertCount(2, $result);
        $this->assertContains('ACS', $result);
        $this->assertContains('113', $result);


        $result = \App\Utilities\ExcelParser::split($string2);
        $this->assertCount(2, $result);
        $this->assertContains('ACS', $result);
        $this->assertContains('113', $result);


        $result = \App\Utilities\ExcelParser::split($string3);
        $this->assertCount(2, $result);
        $this->assertContains('ACS', $result);
        $this->assertContains('113', $result);

    }

    public function testGetDate()
    {
        $path = storage_path('testing/excel-new.xlsx');
        /** @var \Maatwebsite\Excel\Collections\SheetCollection $result */
        $result = Excel::load($path, function (LaravelExcelReader $reader) {
            $reader->noHeading();
        })->get();

        $sheet = $result->get(0);

        $this->assertNotNull($sheet);

        \App\Utilities\ExcelParser::$j = 3;
        $date = \App\Utilities\ExcelParser::getDate($sheet, 2);

        $this->assertNotNull($date);
        $this->assertNotEmpty($date);
        $this->assertRegExp('/[\d]+\/[\d]+\/[\d]+/', $date);

    }

    public function testGetDateTimeDetails()
    {
        $path = storage_path('testing/excel-new.xlsx');
        /** @var \Maatwebsite\Excel\Collections\SheetCollection $result */
        $result = Excel::load($path, function (LaravelExcelReader $reader) {
            $reader->noHeading();
        })->get();

        $sheet = $result->get(0);

        $this->assertNotNull($sheet);

        \App\Utilities\ExcelParser::$i = 7;
        \App\Utilities\ExcelParser::$j = 2;
        $dateTime = \App\Utilities\ExcelParser::getDateTimeDetails($sheet);
        $this->assertNotNull($dateTime);
        $this->assertNotEmpty($dateTime);
        $this->assertRegExp('/[\d]+\/[\d]+\/[\d]+\s[\d]+(?:\\.|:)[\d]+[apm]+/i', $dateTime);
    }

    public function testGetShift()
    {
        $result = \App\Utilities\ExcelParser::getShift('Athi River');
        $this->assertEquals('athi', $result);
        $result = \App\Utilities\ExcelParser::getShift('NRB day');
        $this->assertEquals('day', $result);
        $result = \App\Utilities\ExcelParser::getShift('NRB evening');
        $this->assertEquals('evening', $result);
        $result = \App\Utilities\ExcelParser::getShift('ATHIRIVER');
        $this->assertEquals('athi', $result);
        $result = \App\Utilities\ExcelParser::getShift('NAIROBIDAY');
        $this->assertEquals('day', $result);
        $result = \App\Utilities\ExcelParser::getShift('NAIROBI EVENING');
        $this->assertEquals('evening', $result);
    }

    public function testGetDetails()
    {
        $path = storage_path('testing/excel-new.xlsx');
        /** @var \Maatwebsite\Excel\Collections\SheetCollection $result */
        $result = Excel::load($path, function (LaravelExcelReader $reader) {
            $reader->noHeading();
        })->get();

        $sheet = $result->get(0);

        $this->assertNotNull($sheet);

        \App\Utilities\ExcelParser::$i = 7;
        \App\Utilities\ExcelParser::$j = 2;

        $details = \App\Utilities\ExcelParser::getDetails($sheet);

        $this->assertNotNull($details);
        $this->assertNotEmpty($details);
        $this->assertArrayHasKey('shift', $details);
        $this->assertArrayHasKey('room', $details);
        $this->assertArrayHasKey('dateTime', $details);
        $this->assertEquals($details['room'], 'LR14');
    }

    /*public function testSaveToDB()
    {
        $file = new UploadedFile(storage_path('testing/excel-new.xlsx'), 'excel-new.xlsx', null, filesize(storage_path('testing/excel-new.xlsx')), null, true);
        $this->json("POST", 'api/v1/files/db',
            ["file" => $file])
            ->assertResponseStatus(200);
    }*/

}