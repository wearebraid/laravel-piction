<?php

namespace Wearebraid\Piction\Commands;

use Wearebraid\Piction\Piction;
use Illuminate\Console\Command;
use Wearebraid\Piction\Models\Collection;

class PictionCollections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'piction:collections';

    protected $piction;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve and store collection information';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Retrieving collections from Piction...\n");
        $this->piction = new Piction();
        $collections = $this->piction->getCollections();

        if ($collections) {
            $ids = [];
            $this->info("Found " . count($collections['collections'])." " .
                str_plural('collections', count($collections['collections'])));
            
            foreach ($collections['collections'] as $collection) {
                $ids[] = $collection['aid'];

                $c = Collection::where('collection_id', $collection['aid'])->first();

                if ($c) {
                    $this->info("Updating: " . $collection['aid'] . " => '" .
                        $collection['collection'] . "'");
                    $c->title = $collection['collection'];
                    $c->save();
                } else {
                    $this->info("Creating: " . $collection['aid'] . " => '" .
                        $collection['collection'] . "' and retrieving all records");
                    Collection::create([
                        'collection_id' => $collection['aid'],
                        'title' => $collection['collection']
                    ]);
                }

                $this->call('piction:records', [
                    'collection' => $collection['aid'],
                    '--surl' => $this->piction->getSURL(),
                ]);
            }

            // DELETE COLLECTIONS THAT NO LONGER EXIST
            $delCollections = Collection::whereNotIn('collection_id', $ids)->get();
            foreach ($delCollections as $dc) {
                $this->info("Collection '" . $dc->title ."' no longer exists, deleting...");
                foreach ($dc->records as $r) {
                    $r->delete();
                }
                $dc->delete();
            }

            $this->call('piction:deleted', [
                '--surl' => $this->piction->getSURL(),
            ]);
        } else {
            $this->error('ERROR: Could not retrieve data.');
        }
    }
}
