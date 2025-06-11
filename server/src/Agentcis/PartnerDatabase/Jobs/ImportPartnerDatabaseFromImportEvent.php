<?php

namespace Agentcis\PartnerDatabase\Jobs;

use Agentcis\PartnerDatabase\Imports\PartnerDatabaseImport;
use Agentcis\PartnerDatabase\Model\ImportEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ImportPartnerDatabaseFromImportEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;
    /**
     * @var ImportEvent
     */
    private $importEvent;

    /**
     * ImportPartnerDatabaseFromImportEvent constructor.
     * @param ImportEvent $importEvent
     */
    public function __construct(ImportEvent $importEvent)
    {
        $this->importEvent = $importEvent;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->importEvent->setAttribute('status', 1);
        $this->importEvent->save();
        Excel::import(new PartnerDatabaseImport($this->importEvent), $this->importEvent->file_path, 's3');

        $this->importEvent->setAttribute('status', 2);
        $this->importEvent->save();
    }
}
