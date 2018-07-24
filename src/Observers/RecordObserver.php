<?php

namespace Braid\Piction\Observers;

use Braid\Piction\Models\Record;
use Illuminate\Support\Facades\Log;

class RecordObserver
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
