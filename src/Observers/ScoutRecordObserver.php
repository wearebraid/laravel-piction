<?php

namespace Wearebraid\Piction\Observers;

use Illuminate\Support\Facades\Log;
use Wearebraid\Piction\Models\Scout\Record;

class ScoutRecordObserver
{
    /**
     * Handle the record "saving" event.
     *
     * @param  \Wearebraid\Piction\Models\Record  $record
     * @return void
     */
    public function saving(Record $record)
    {
        $record->active = $record->setActiveStatus();
    }
}
