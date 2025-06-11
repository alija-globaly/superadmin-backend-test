<?php

namespace Agentcis\PartnerDatabase\Imports;

use Agentcis\PartnerDatabase\Jobs\ImportProductJob;
use Agentcis\PartnerDatabase\Model\ImportEvent;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToCollection, WithHeadingRow, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    /**
     * @var ImportEvent
     */
    private $importEvent;

    /**
     * ProductImport constructor.
     * @param ImportEvent $importEvent
     */
    public function __construct(ImportEvent $importEvent)
    {
        $this->importEvent = $importEvent;
    }

    public function collection(Collection $rows)
    {
        $rows
            ->filter(function ($row) {
                return $row->filter()->isNotEmpty();
            })
            ->map(function ($row) {
                $row['partner_email'] = preg_replace('/\s+/', '', $row['partner_email']);
                $row['product_name'] = trim($row['product_name']);
                return $row;
            })
            ->groupBy('partner_email')
            ->map(function (Collection $products) {
                return $products->groupBy('product_name')
                    ->map(function (Collection $row) {
                        $product = $row->first();
                        $product['branches'] = $row->pluck('branch_name')->flatten()->unique()->toArray();
                        return $product;
                    });
            })
            ->chunk(6)
            ->each(function ($chunk) {
                ImportProductJob::dispatch($this->importEvent, $chunk)->onQueue('import');
            });
    }

    public function headingRow(): int
    {
        return 1;
    }
}
