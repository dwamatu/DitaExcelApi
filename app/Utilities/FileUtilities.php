<?php
/**
 * Created by PhpStorm.
 * User: black
 * Date: 08/05/2017
 * Time: 09:43
 */


namespace App\Utilities;


use Illuminate\Support\Facades\Storage;

class FileUtilities
{
    public static function getFile($params, $photo, $image)
    {
        $result = null;
        if ($image != null) {
            if ($photo === 'avatar') {
                $path = storage_path() . "/app/$params/avatars/" . $image;
                $result = file_exists($path) ? $path : null;
            } elseif ($photo === 'thumbnail') {
                $path = storage_path() . "/app/$params/avatars/thumbnails/" . $image;
                $result = file_exists($path) ? $path : null;
            }
        }

        return $result;
    }

    public static function storeFile($resource,$filename)
    {
        //Store Avatar
        $path = Storage::putFileAs(
            "files/", $resource, $filename
        );
    }
}
