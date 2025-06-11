<?php

namespace Agentcis\PartnerDatabase\Imports;

use Agentcis\PartnerDatabase\Model\Category;
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

class PartnerImport implements ToCollection, WithHeadingRow, SkipsOnFailure, SkipsOnError, WithValidation
{
    use SkipsFailures, SkipsErrors;

    /**
     * @var ImportEvent
     */
    private $importEvent;

    /**
     * PartnerImport constructor.
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
            $row['email'] = preg_replace('/\s+/', '', $row['email']);
            $row['partner_name'] = preg_replace('/\s+/', ' ', $row['partner_name']);
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
            ->map(function ($row) {
                $values = [
                    'name' => preg_replace('/\s+/', ' ', $row['partner_name']),
                    'registration_number' => $row['registration_number'],
                    'currency_code' => $row['currency_code'],
                    'street' => $row['street'],
                    'city' => $row['city'],
                    'logo' => '',
                    'state' => $row['state'],
                    'zip_code' => $row['zip_code'],
                    'country' => Country::query()->where('country_name', $row['country'])->firstOrFail()->getKey(),
                    'phone_number' => $row['phone_number'],
                    'website' => $row['website'],
                    'category_id' => Category::query()->where('name', $row['partner_type'])->where('type',
                        'partner')->firstOrFail()->getKey(),
                ];

//                if(!empty($row['partner_logo'])) {
//                    try {
//                        $content = file_get_contents($row['partner_logo'], false, stream_context_create([
//                            "ssl" => [
//                                "verify_peer" => false,
//                                "verify_peer_name" => false,
//                            ],
//                        ]));
//                        $size = getimagesize($row['partner_logo']);
//                        $extension = image_type_to_extension($size[2]);
//                        $logoName = Str::random(40) . $extension;
////                    Storage::put('import/partner/logo/'. $logoName, $content, ['visibility' => 'public']);
////                    $values['logo'] = 'https://agentcisapp.s3.ap-southeast-2.amazonaws.com/import/partner/logo/' . $logoName;
//
//                    }catch (\Exception $e) {
//
//                    }
//                }
                return tap(Partner::query()->firstOrNew(['email' => preg_replace('/\s+/', '', $row['email'])]),
                    function ($instance) use ($values) {
                        $instance->fill($values);
                    });
            });
        $updatables = $models->filter(function (Partner $row) {
            return $row->exists;
        });
        $updatables->each(function (Partner $partner) {
            try {
                $partner->saveOrFail();
            } catch (Throwable $e) {
                $this->onError($e);
            }
        });

        $insertables = $models->filter(function (Partner $row) {
            return !$row->exists;
        });
        try {
            Partner::query()->insert(
                $insertables->map(function (Partner $partner) {
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
        $report['sheets']['partners'] = [
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

    public function rules(): array
    {
        return [
            'partner_name' => 'required',
            'email' => 'required|email',
            'partner_type' => [
                'required',
                Rule::exists('categories', 'name')->where(function ($query) {
                    $query->where('type', 'partner');
                })
            ],
            'currency_code' => ['required', Rule::exists('agentcis.countries', 'currency_code')],
            'country' => ['required', Rule::exists('agentcis.countries', 'country_name')],
        ];
    }
}
