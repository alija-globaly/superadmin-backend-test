<?php

namespace Agentcis\PartnerDatabase\Jobs;

use Agentcis\PartnerDatabase\DegreeLevels;
use Agentcis\PartnerDatabase\FeeTerms;
use Agentcis\PartnerDatabase\FeeTypes;
use Agentcis\PartnerDatabase\Model\Category;
use Agentcis\PartnerDatabase\Model\ImportEvent;
use Agentcis\PartnerDatabase\Model\Partner;
use Agentcis\PartnerDatabase\Model\Product;
use Agentcis\PartnerDatabase\SubjectAreas;
use Agentcis\PartnerDatabase\Subjects;
use DateTime;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Exceptions\RowSkippedException;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\RowValidator;
use Throwable;

class ImportProductJob implements ShouldQueue, SkipsOnFailure, SkipsOnError, WithValidation
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, SkipsFailures, SkipsErrors;

    private $products;
    private $updatable = 0;
    private $insertable = 0;
    /**
     * @var ImportEvent
     */
    private $importEvent;

    private $productFeeTypes = [
        [
            "id" => 1,
            "name" => "accommodation_fee"
        ],
        [
            "id" => 2,
            "name" => "administration_fee"
        ],
        [
            "id" => 3,
            "name" => "airline_ticket"
        ],
        [
            "id" => 4,
            "name" => "airport_transfer_fee"
        ],
        [
            "id" => 5,
            "name" => "application_fee"
        ],
        [
            "id" => 6,
            "name" => "bond"
        ],
        [
            "id" => 7,
            "name" => "exam_fee"
        ],
        [
            "id" => 8,
            "name" => "date_change_fee"
        ],
        [
            "id" => 9,
            "name" => "extension_fee"
        ],
        [
            "id" => 10,
            "name" => "extra_fee"
        ],
        [
            "id" => 11,
            "name" => "FCE_exam_fee"
        ],
        [
            "id" => 12,
            "name" => "health_cover"
        ],
        [
            "id" => 13,
            "name" => "i20_Fee"
        ],
        [
            "id" => 14,
            "name" => "instalment_fee"
        ],
        [
            "id" => 15,
            "name" => "key_deposit_fee"
        ],
        [
            "id" => 16,
            "name" => "late_payment_fee"
        ],
        [
            "id" => 17,
            "name" => "material_deposit"
        ],
        [
            "id" => 18,
            "name" => "material_fee"
        ],
        [
            "id" => 19,
            "name" => "medical_exam"
        ],
        [
            "id" => 20,
            "name" => "placement_fee"
        ],
        [
            "id" => 21,
            "name" => "security_deposit_fee"
        ],
        [
            "id" => 22,
            "name" => "service_fee"
        ],
        [
            "id" => 23,
            "name" => "swipe_card_fee"
        ],
        [
            "id" => 24,
            "name" => "training_fee"
        ],
        [
            "id" => 25,
            "name" => "transaction_fee"
        ],
        [
            "id" => 26,
            "name" => "translation_fee"
        ],
        [
            "id" => 27,
            "name" => "travel_insurance"
        ],
        [
            "id" => 28,
            "name" => "tuition_fee"
        ],
        [
            "id" => 29,
            "name" => "visa_counseling"
        ],
        [
            "id" => 30,
            "name" => "visa_fee"
        ],
        [
            "id" => 31,
            "name" => "visa_process"
        ],
        [
            "id" => 32,
            "name" => "RMA_fee"
        ],
        [
            "id" => 33,
            "name" => "registered_migration_agent_fee"
        ]
    ];

    /**
     * ImportProductJob constructor.
     * @param ImportEvent $importEvent
     * @param Collection $products
     */
    public function __construct(ImportEvent $importEvent, Collection $products)
    {
        $this->products = $products;
        $this->importEvent = $importEvent;
    }

    public function handle()
    {
        $this->products->each(function (Collection $products, $partnerEmail) {
            try {
                $partner = Partner::query()
                    ->where('email', preg_replace('/\s+/', '', $partnerEmail))
                    ->select('id', 'email')
                    ->firstOrFail();

            } catch (Exception $e) {
                $failures[] = new Failure(
                    1,
                    'partner_email',
                    ['Partner with email: ' . $partnerEmail . ' doesn\'t exist'],
                    []
                );
                $this->onFailure(...$failures);
                return;
            }

            try {
                $validator = app(RowValidator::class);
                $validator->validate($products->values()->toArray(), $this);
            } catch (RowSkippedException $e) {
                $products = $products->values()->reject(function ($row, $key) use ($e) {
                    return in_array($key, $e->skippedRows());
                });
            }

            $products->each(function ($product) use ($partner) {
                $attributes = [
                    'partner_id' => $partner->getKey(),
                    'name' => $product['product_name']
                ];
                $values = [
                    'duration' => $product['duration'],
                    'description' => $product['description'],
                    'category_id' => Category::query()->where('name', $product['product_type'])
                        ->where('type', 'product')->firstOrFail()->getKey(),
                    'english_test_score' => [
                        'TOEFL' => [
                            'Listening' => '',
                            'Reading' => '',
                            'Writing' => '',
                            'Speaking' => '',
                            'Overall' => $product['toefl'] ?? '',
                        ],
                        'IELTS' => [
                            'Listening' => '',
                            'Reading' => '',
                            'Writing' => '',
                            'Speaking' => '',
                            'Overall' => $product['ielts'] ?? '',
                        ],
                        'PTE' => [
                            'Listening' => '',
                            'Reading' => '',
                            'Writing' => '',
                            'Speaking' => '',
                            'Overall' => $product['pte'] ?? '',
                        ],
                    ],
                    'fees' => null,
                    'academic_requirement' => [
                        'degree_level' => collect((new DegreeLevels())->toArray())->firstWhere('name', '=',
                            $product['requirement_degree_level']),
                        'academic_score_type' => $product['academic_type'],
                        'academic_score' => $product['academic_score'],
                    ],
                    'subject_area_and_level' => [
                        'subject_area' => collect((new SubjectAreas())->toArray())->firstWhere('name', '=',
                            $product['subject_area']),
                        'subject' => collect((new Subjects())->toArray())->firstWhere('name', '=',
                            $product['subject']),
                        'degree_level' => collect((new DegreeLevels())->toArray())->firstWhere('name', '=',
                            $product['degree_level']),
                    ]
                ];

                if($product['intake_month']) {
                    try {
                        $value['intake_month'] = collect(array_filter(explode(',', preg_replace('/\s+/', '',
                            str_replace('.', ',', $product['intake_month'])))))->map(function ($month) {
                            return (int)DateTime::createFromFormat('!M', $month)->format('n');
                        });
                    }catch (Exception $e) {
                        $failures[] = new Failure(
                            1,
                            'intake_month',
                            ['Invalid intake month value : '. $product['intake_month']],
                            $product
                        );
                        $this->onFailure(...$failures);
                    }
                }
                $fee = [
                    'country' => 'All Countries',
                    'name' => 'Default Fee'
                ];

                foreach ($this->productFeeTypes as $feeType) {
                    if (!empty($product[$feeType['name'] . '_type_product_fee'])) {
                        $instalment = (int)$product[$feeType['name'] . '_type_installments'];
                        $amount = $product[$feeType['name'] . '_type_amount'] ?? 0;
                        $fee['fee_items'][] = [
                            'inQuotation' => true,
                            'amount' => $amount,
                            'totalFee' => $instalment * $amount,
                            'instalment' => $instalment,
                            'fee_type' => collect((new FeeTypes())->toArray())->firstWhere('name',
                                $product[$feeType['name'] . '_type_product_fee']),
                        ];
                    }
                }
                if (!empty($product['installment_type'])) {
                    $fee['feeTerms'] = collect((new FeeTerms())->toArray())->firstWhere('name',
                        $product['installment_type']);
                }

                if (!empty($fee['fee_items'])) {
                    $values['fees'] = $fee;
                }
                /** @var Product $iProduct */
                $iProduct = tap(Product::query()->firstOrNew($attributes),
                    function ($instance) use ($attributes, $values) {
                        $instance->fill($values);
                        $instance->forceFill([
                            'partner_id' => $attributes['partner_id'],
                            'english_test_score' => $values['english_test_score'],
                            'subject_area_and_level' => $values['subject_area_and_level'],
                            'academic_requirement' => $values['academic_requirement'],
                            'fees' => $values['fees'],
                        ]);
                    });

                $branches = DB::table('partner_branches')
                    ->where('partner_id', $partner->getKey())
                    ->whereIn('name', $product['branches'])
                    ->select('id', 'name')
                    ->get()
                    ->map(function ($branch) {
                        return $branch->id;
                    });
                if (count($product['branches']) !== count($branches)) {
                    $failures[] = new Failure(
                        1,
                        'branches',
                        ['Some of provided branches for product: ' . $product['product_name'] . ' are not available on partner : ' . $partner->getAttribute('email')],
                        $product['branches']
                    );
                    $this->onFailure(...$failures);
                }
                if ($branches->isEmpty()) {
                    $failures[] = new Failure(
                        1,
                        'branches',
                        ['Provided branches for product: ' . $product['product_name'] . ' are invalid'],
                        $product['branches']
                    );
                    $this->onFailure(...$failures);
                } else {
                    try {
                        $this->updatable += $iProduct->exists ? 1 : 0;
                        $this->insertable += !$iProduct->exists ? 1 : 0;
                        $iProduct->saveOrFail();
                        $iProduct->fresh();
                        $iProduct->branches()->sync($branches->toArray());
                    } catch (Throwable $e) {
                        $this->onError($e);
                    }
                }
            });
        });

        $event = $this->importEvent->fresh();
        $report = $event->report ?? [];
        $productSheet = $report['sheets']['products'] ?? ['stats' => ['updatable' => 0, 'insertable' => 0]];
        if ($this->errors()->count()) {
            $report['sheets']['products']['errors'] = $this->errors()->map(function($error) {
                return ['message' => $error->__toString(), 'trace' => $error->getTraceAsString()];
            })->merge($productSheet['errors'] ?? []);
        } elseif (empty($report['sheets']['products']['errors'])) {
            $report['sheets']['products']['errors'] = [];
        }
        if ($this->failures()->count()) {
            $report['sheets']['products']['failures'] = $this->failures()
                ->map(function (Failure $failure) {
                    return [
                        'values' => $failure->values(),
                        'errors' => collect($failure->errors())->map(function ($message) use ($failure) {
                            if ($failure->attribute() === 'branches') {
                                return $message;
                            }
                            if ($failure->attribute() === 'intake_month') {
                                return $message;
                            }
                            if ($failure->attribute() === 'partner_email') {
                                return $message;
                            }
                            return __('There was an error on value :value. :message',
                                ['value' => $failure->values()[$failure->attribute()], 'message' => $message]);
                        })->all()
                    ];
                })->merge($productSheet['failures'] ?? []);
        } else {
            if (empty($report['sheets']['products']['failures'])) {
                $report['sheets']['products']['failures'] = [];
            }
        }

        $report['sheets']['products']['stats'] = [
            'updatable' => $productSheet['stats']['updatable'] + $this->updatable,
            'insertable' => $productSheet['stats']['insertable'] + $this->insertable,
        ];
        $this->importEvent->setAttribute('report', $report);
        $this->importEvent->save();
    }

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            'product_name' => 'required',
            'branch_name' => 'required',
            'partner_email' => 'required|email',
            'product_type' => [
                'required',
                Rule::exists('categories', 'name')->where(function ($query) {
                    $query->where('type', 'product');
                })
            ],
            'requirement_degree_level' => [
                'nullable',
                Rule::in(collect((new DegreeLevels())->toArray())->pluck('name'))
            ],
            'academic_type' => ['nullable', Rule::in(['percentage', 'GPA'])],
            'academic_score' => ['required_with:academic_type', 'nullable'],
            'subject_area' => ['nullable', Rule::in(collect((new SubjectAreas())->toArray())->pluck('name'))],
            'subject' => ['nullable', Rule::in(collect((new Subjects())->toArray())->pluck('name'))],
            'degree_level' => ['nullable', Rule::in(collect((new DegreeLevels())->toArray())->pluck('name'))],
        ];
    }
}
