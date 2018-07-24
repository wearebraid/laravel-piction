<?php

namespace Braid\Piction\Observers;

use Illuminate\Support\Facades\Log;
use Braid\Piction\Models\Scout\Record;

class ScoutRecordObserver
{
    /**
     * Handle the record "saving" event.
     *
     * @param  \Braid\Piction\Models\Record  $record
     * @return void
     */
    public function saving(Record $record)
    {
        $record->active = $record->setActiveStatus();
    }
}
