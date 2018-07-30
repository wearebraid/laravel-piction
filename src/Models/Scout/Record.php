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
        $data = $this->toArray();
        $metaData = $data['metadata'];
        $data['metadata'] = [];
        foreach ($metaData as $key => $md) {
            array_set($data, 'metadata.' . $key, $md);
        }
        return $data;
    }

    public function getScoutKey()
    {
        return $this->id;
    }
}
