<?php

namespace Wearebraid\Piction\Models\Scout;

use Laravel\Scout\Searchable;

class Record extends \Wearebraid\Piction\Models\Record
{
    use Searchable;

    public function searchableAs()
    {
        return 'records';
    }

    public function toSearchableArray()
    {
        return $this->toArray();
    }

    public function getScoutKey()
    {
        return $this->id;
    }
}
