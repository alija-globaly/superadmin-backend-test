<?php

namespace Agentcis\PartnerDatabase\Imports;

use Agentcis\PartnerDatabase\Model\Branch;
use Agentcis\PartnerDatabase\Model\Country;
use Agentcis\PartnerDatabase\Model\ImportEvent;
use Agentcis\PartnerDatabase\Model\Partner;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Exceptions\RowSkippedException;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\RowValidator;
use Throwable;

class BranchImport implements ToCollection, WithHeadingRow, SkipsOnError, SkipsOnFailure, WithValidation
{
    use SkipsErrors, SkipsFailures;

    /**
     * @var ImportEvent
     */
    private $importEvent;

    /**
     * BranchImport constructor.
     * @param ImportEvent $importEvent
     */
    public function __construct(ImportEvent $importEvent)
    {
        $this->importEvent = $importEvent;
    }
    public function collection(Collection $rows)
    {
        $collection = $rows->filter(function ($row) {
            return $row->filter()->isNotEmpty();
        })->map(function ($row) {
            $row['partner_email'] = preg_replace('/\s+/', '', $row['partner_email']);
            $row['branch_email'] = preg_replace('/\s+/', '', $row['branch_email']);
            $row['branch_name'] = preg_replace('/\s+/', ' ', $row['branch_name']);
            return $row;
        });

        try {
            $validator = app(RowValidator::class);
            $validator->validate($collection->toArray(), $this);
        } catch (RowSkippedException $e) {
            $collection = $collection->reject(function ($row, $key) use ($e) {
                return in_array($key, $e->skippedRows());
            });
        }

        $models = $collection
            ->groupBy('partner_email')
            ->map(function ($branches, $partnerEmail) {
                $partner = Partner::query()->where('email', preg_replace('/\s+/', '', $partnerEmail))->firstOrFail();

                return $branches->map(function ($row) use ($partner) {
                    $attributes = [
                        'email' => preg_replace('/\s+/', '', $row['branch_email']),
                        'name' => $row['branch_name'],
                        'partner_id' => $partner->getKey(),
                    ];

                    $values = [
                        'phone_number' => $row['phone_number'],
                        'type' => strtolower($row['head_office']) === 'yes' ? Branch::HEAD_OFFICE : Branch::OTHER_OFFICE,
                        'street' => $row['street'],
                        'city' => $row['city'],
                        'state' => $row['state'],
                        'zip_code' => $row['zip_code'],
                        'country' => Country::query()->where('country_name', $row['country'])->firstOrFail()->getKey(),
                    ];
                    return tap(Branch::query()->firstOrNew($attributes),
                        function ($instance) use ($attributes, $values) {
                            $instance->fill($values);
                            $instance->forceFill(['partner_id' => $attributes['partner_id']]);
                        });
                });
            })->flatten();

        $updatables = $models->filter(function (Branch $row) {
            return $row->exists;
        });
        $updatables->each(function (Branch $branch) {
            try {
                $branch->saveOrFail();
            } catch (Throwable $e) {
                $this->onError($e);
            }
        });

        $insertables = $models->filter(function (Branch $row) {
            return !$row->exists;
        });
        try {

            Branch::query()->insert(
                $insertables->map(function (Branch $partner) {
                    $time = $partner->freshTimestamp();
                    $partner->setCreatedAt($time);
                    $partner->setUpdatedAt($time);
                    return $partner;
                })->toArray()
            );
        } catch (Throwable $e) {
            $this->onError($e);
        }

        $report = $this->importEvent->report ?? [];
        $report['sheets']['branches'] = [
            'errors' => $this->errors(),
            'failures' => $this->failures()->map(function(Failure $failure) { return ['values' => $failure->values(), 'errors' => $failure->toArray()];}),
            'stats' => [
                'updatable' => $updatables->count(),
                'insertable' => $insertables->count(),
            ]
        ];
        $this->importEvent->setAttribute('report', $report);
        $this->importEvent->save();
    }

    public function headingRow(): int
    {
        return 1;
    }

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            'partner_email' => [
                'required',
                Rule::exists('partners', 'email'),
            ],
            'branch_email' => 'required|email',
            'country' => ['required', Rule::exists('agentcis.countries', 'country_name')],
        ];
    }
}
