<?php

namespace Agentcis\PartnerDatabase\Imports;

use Agentcis\PartnerDatabase\Model\ImportEvent;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;

class PartnerDatabaseImport implements WithMultipleSheets, SkipsUnknownSheets
{
    private $importEvent;

    /**
     * PartnerDatabaseImport constructor.
     * @param ImportEvent $importEvent
     */
    public function __construct(ImportEvent $importEvent)
    {
        $this->importEvent = $importEvent;
    }
    /**
     * Concern With @Bishal Da
     *    While importing product sheet branch sync strategy (sync without deatching branches)
     *    Academic score type and it value formatting
     *    SuperAdmin live data issues
     *    Product sheet row number 123, 1050 for quick ref
     *    Check subject and subject area map
     * Concern for me
     *    @RESOLVED Performance issue in product sheet parsing
     *    @TODO Push the execution in queue for better performance
     *    @TODO Performance issue in product sheet execution
     *    @TODO Product fee column parsing and exec
     *    @TODO Upload partner logo from sheet url (On hold as it need to be tested on queue and upload on S3 storage)
     *    @TODO Report of the import task in as much detail as possible.
     */
    public function sheets(): array
    {
        return [
            0 => new PartnerImport($this->importEvent),
            1 => new BranchImport($this->importEvent),
            2 => new ProductImport($this->importEvent),
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        info("Sheet {$sheetName} was skipped");
    }
}
