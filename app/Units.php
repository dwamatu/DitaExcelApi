<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Units extends Model
{
    protected $table = 'units';

    protected $fillable = ['name', 'room', 'date','shift','section'];
}
