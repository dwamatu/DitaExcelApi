<?php
/**
 * Created by PhpStorm.
 * User: black
 * Date: 08/05/2017
 * Time: 09:43
 */


namespace App\Utilities;


use App\File;
use Illuminate\Support\Facades\Storage;

class FileUtilities
{
    public static function getFile($file_id)
    {

        $result = null;
        //Get the most recent File with the same extension
        $fileDetails = File::whereHas('type', function ($query) use ($file_id) {
            $query->where('id', '=', $file_id);
        })->orderBy('created_at', 'desc')->first();

        $file = $fileDetails->file_name;

        if ($file != null) {
            $path = storage_path() . "/app/files/" . $file;
            $result = file_exists($path) ? $path : null;
        }
        return $result;
    }

    public static function getDetails($file_id)
    {
        return File::whereHas('type', function ($query) use ($file_id) {
            $query->where('id', '=', $file_id);
        })->orderBy('created_at', 'desc')->first();
    }

    public static function storeFile($resource, $filename)
    {
        //Store File
        $path = Storage::putFileAs(
            "files/", $resource, $filename
        );
    }

    public static function storeFileCloud($filename, $resource)
    {
        Storage::disk('google')->write($filename, $resource);
    }

    public static function getFileCloud($file_id)
    {
        $result = null;
        //Get the most recent File with the same extension
        $fileDetails = File::whereHas('type', function ($query) use ($file_id) {
            $query->where('id', '=', $file_id);
        })->orderBy('created_at', 'desc')->first();

        $filename = $fileDetails->file_name;

        $listContents = Storage::disk('google')->listContents();
        $details = FileUtilities::getFileCloudDetails($listContents, $filename);
        $data = Storage::disk('google')->get($details['path']);
        $path = storage_path() . '/app/files/' . 'temp_' . $filename;
        \Illuminate\Support\Facades\File::put($path, $data);
        return $path;
    }

    static function getFileCloudDetails(Array $array, $filename)
    {

        foreach ($array as $subarray) {
            $filename2 = $subarray['filename'] . '.' . $subarray['extension'];
            if (strcmp($filename2, $filename) == 0) {
                return $subarray;
            }
        }

        return null;
    }
}
