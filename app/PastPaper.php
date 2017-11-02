<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PastPaper extends Model
{
    protected $table ='Papers_Resource';

    protected $fillable =['name','resource_type','filepath'];
}
