<?php

namespace Agentcis\PartnerDatabase\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $casts = [
        'english_test_score' => 'json',
        'intake_month' => 'json',
        'academic_requirement' => 'json',
        'fees' => 'json',
        'subject_area_and_level' => 'json',
        'other_test_score' => 'json'
    ];

    protected $fillable = [
        'name',
        'category_id',
        'description',
        'duration',
        'intake_month'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branches_products', 'product_id', 'branch_id')
            ->orderBy('type')->withTrashed();
    }

    public function getOtherTestScoreAttribute($value)
    {
        if (null === $value || empty(json_decode($value))) {
            return [
                'SAT I' => '',
                'SAT II' => '',
                'GRE' => '',
                'GMAT' => '',
            ];
        }

        return json_decode($value);
    }

    public function getEnglishTestScoreAttribute($value)
    {
        if (empty($value)) {
            $bands = [
                'Listening' => '',
                'Reading' => '',
                'Writing' => '',
                'Speaking' => '',
                'Overall' => '',
            ];
            return [
                'TOEFL' => $bands,
                'IELTS' => $bands,
                'PTE' => $bands,
            ];
        }
//        $partner = Partner::query()->where('email', preg_replace('/\s+/', '', $partnerEmail))->firstOrFail();

        return json_decode($value, true);

    }

    public function getIntakeMonthAttribute($months)
    {
        if (empty($months)) {
            return [];
        }
        $result = [];
        foreach (json_decode($months) as $month) {
            $result[] = [
                'id' => $month,
                'value' => \DateTime::createFromFormat('!m', $month + 1)->format('F')
            ];
        }
        return $result;
    }
}
