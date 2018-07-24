<?php

namespace Wearebraid\Piction\Observers;

use Wearebraid\Piction\Models\Record;
use Illuminate\Support\Facades\Log;

class RecordObserver
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
