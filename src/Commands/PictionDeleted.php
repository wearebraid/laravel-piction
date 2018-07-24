<?php

namespace Wearebraid\Piction\Commands;

use Illuminate\Console\Command;
use Wearebraid\Piction\Models\Record;
use Wearebraid\Piction\Facades\Piction;
use Symfony\Component\Console\Helper\ProgressBar;

class PictionDeleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'piction:deleted';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve all deleted UMOs then remove from system';

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
        $this->info("Attempting authentication to Piction...\n");
        $umos = Piction::getDeletedUmos();

        if ($umos) {
            $this->info(count($umos) . " deleted " .
                str_plural('record', count($umos)));
            
            $recordModel = Piction::recordModel();
            $records = $recordModel::whereIn('umo_id', $umos)->get();

            $max = count($records);
            $this->info($max . " " . str_plural('record', $max) .
                " to be deleted from database");
            
            if ($max > 0) {
                $progress = new ProgressBar($this->output, $max);
                $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% '.
                    '%elapsed:6s%/%estimated:-6s% %memory:6s%');
                $progress->start();
                foreach ($records as $record) {
                    $record->delete();
                    $progress->advance();
                }
                $progress->finish();
            }
        }
    }
}
