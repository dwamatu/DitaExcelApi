<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PastPapers extends Model
{
    protected $table ='Papers_Resource';

    protected $fillable =['name','resource_type','filepath'];
}
