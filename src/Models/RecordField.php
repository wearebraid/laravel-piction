<?php

namespace Wearebraid\Piction\Models;

use Illuminate\Database\Eloquent\Model;

class RecordField extends Model
{
    protected $fillable = ['tag', 'title'];
    public $timestamps = false;
}
