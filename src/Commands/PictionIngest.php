<?php

namespace Wearebraid\Piction\Commands;

use Carbon\Carbon;
use Wearebraid\Piction\Piction;
use Illuminate\Console\Command;
use Wearebraid\Piction\Models\Collection;
use Wearebraid\Piction\Models\RecordField;
use Symfony\Component\Console\Helper\ProgressBar;

class PictionIngest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'piction:records {collection} {--surl=}';

    protected $fields = [];

    protected $piction;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ingest latest data from piction';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function ingestRecord($record)
    {
        $recUpdate = [
            'umo_id' => $record['id'],
            'collection_id' => $record['a'],
            'type' => $record['t'],
            'caption' => $record['c'],
            'thumbnail' => isset($record['wq'][1]) ? $record['wq'][1]['u'] : null,
            'sort' => isset($record['ob']) ? $record['ob'] : 0,
            'metadata' => [],
        ];

        if (!empty($record['dc'])) {
            $recUpdate['created_at'] = $record['dc'];
        }
        if (!empty($record['du'])) {
            $recUpdate['updated_at'] = $record['du'];
        }

        foreach ($record['m'] as $meta) {
            if (!in_array($meta['i'], config('piction.options.meta.ignore', []))) {
                $tag = $meta['i'] . '.' . $meta['c'];
                $recUpdate['metadata'][$tag] = $meta['v'];
                $this->fields[$tag] = title_case($meta['d']);
            }
        }

        $recordModel = $this->piction->recordModel();
        $recordModel::updateOrCreate(['umo_id' => $record['id']], $recUpdate);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 99999999);
        set_time_limit(0);

        // RETRIEVE SURL
        $this->info("Attempting authentication to Piction...\n");
        $this->piction = new Piction($this->option('surl'));
        $collectionId = $this->argument('collection');
        $surl = $this->piction->getSURL();

        if ($surl) {
            $this->info('SURL Retrieved! ' . $surl . "\n");
        } else {
            $this->info('Could not retrieve SURL');
            die();
        }

        $page = 0;
        $lastpage = 1;
        $max = 0;
        $progressInitiated = false;
        $since = null;

        if ($collectionId) {
            $collection = Collection::where('collection_id', $collectionId)->first();
            if ($collection && $collection->last_updated) {
                $since = $collection->last_updated->format('Y-m-d');
            }
        } else {
            $collectionId = null;
            $allCollections = Collection::orderBy('last_updated', 'asc')->first();
            if ($allCollections && $allCollections->last_updated) {
                $since = $allCollections->last_updated->format('Y-m-d');
            }
        }

        $this->info("Retrieving records...");

        while ($page < $lastpage) {
            $page++;
            $response = $this->piction->getRecords($collectionId, $page, null, $since);
            if ($response !== false) {
                if (!$progressInitiated) {
                    $progressInitiated = true;
                    $max = (int) $response['s']['t'];
                    if ($max > 0) {
                        $this->info($max . " new or updated " . str_plural('record', $max));
                        $lastpage = $response['s']['maxpages'];
                        $progress = new ProgressBar($this->output, $max);
                        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
                        $progress->start();
                    } else {
                        $this->info("No changes.");
                        return;
                    }
                }
                
                foreach ($response['r'] as $rec) {
                    $this->ingestRecord($rec);
                    $progress->advance();
                }
            } else {
                $this->info('Died on page ' . $page);
                die();
            }
        }

        if (isset($progress)) {
            $progress->finish();
        }

        if (!empty($this->fields)) {
            $this->info("\n\nUpdating field names...");
            foreach ($this->fields as $tag => $title) {
                RecordField::updateOrCreate([
                    'tag' => $tag,
                ], [
                    'title' => $title,
                ]);
            }
        }

        if (isset($collection)) {
            $collection->last_updated = Carbon::now();
            $collection->save();
        } else {
            Collection::whereNotNull('collection_id')->update([
                'last_updated' => Carbon::now()->format('Y-m-d')
            ]);
        }
    }
}
