<?php

namespace Wearebraid\Piction\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $table = 'records';
    
    protected $fillable = [
        'umo_id',
        'collection_id',
        'type',
        'metadata',
        'caption',
        'thumbnail',
        'sort',
        'created_at',
        'updated_at'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];
    
    public $timestamps = false;

    protected $casts = [
        'metadata' => 'array',
    ];

    public function collection()
    {
        return $this->belongsTo(
            Wearebraid\Piction\Models\Collection::class,
            'collection_id',
            'collection_id'
        );
    }

    public function setActiveStatus()
    {
        if (!config('piction.options.check_published', false)) {
            return true;
        }
        if (count($this->metadata) > 0) {
            foreach (config('piction.options.published', []) as $key => $value) {
                if (isset($this->metadata[$key]) && $this->metadata[$key] == $value) {
                    return true;
                }
            }
        }
        return false;
    }


    public static function getUniqueMeta($field, $collection = null)
    {
        $data = Record::select('metadata->' . $field)
            ->where('active', 1)
            ->whereNotNull('metadata->' . $field)
            ->distinct();

        if ($collection) {
            $data->where('collection_id', $collection);
        }

        $data = $data->get()
            ->flatten()
            ->mapWithKeys(function ($item) {
                $keys = array_keys($item->toArray());
                $value = json_decode($item[$keys[0]]);
                return [strtolower($value) => $value];
            })
            ->filter(function ($value, $key) {
                return !empty($value);
            })
            ->toArray();

        ksort($data);
        return array_flatten($data);
    }
}
