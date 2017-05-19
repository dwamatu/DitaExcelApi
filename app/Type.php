<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $table = 'filetypes';

    protected $fillable = ['name', 'created_at', 'updated_at'];

}
