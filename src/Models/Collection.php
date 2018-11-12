<?php

namespace Wearebraid\Piction\Models;

use Illuminate\Database\Eloquent\Model;
use Wearebraid\Piction\Facades\Piction;
use Illuminate\Notifications\Notifiable;

class Collection extends Model
{
    use Notifiable;
    protected $fillable = ['collection_id', 'title', 'last_updated'];
    protected $dates = ['last_updated'];
    public $timestamps = false;

    public function routeNotificationForSlack()
    {
        return env('PICTION_SLACK_WEBHOOK');
    }

    public function records()
    {
        return $this->hasMany(
            Piction::recordModel(),
            'collection_id',
            'collection_id'
        );
    }
}
