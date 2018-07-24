<?php

namespace Wearebraid\Piction\Models;

use Wearebraid\Piction\Facades\Piction;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = ['collection_id', 'title', 'last_updated'];
    protected $dates = ['last_updated'];
    public $timestamps = false;

    public function records()
    {
        return $this->hasMany(
            Piction::recordModel(),
            'collection_id',
            'collection_id'
        );
    }
}
