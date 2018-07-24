<?php

namespace Braid\Piction\Models\Scout;

use Laravel\Scout\Searchable;

class Record extends \Braid\Piction\Models\Record
{
    use Searchable;

    public function searchableAs()
    {
        return $this->collection_id;
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
