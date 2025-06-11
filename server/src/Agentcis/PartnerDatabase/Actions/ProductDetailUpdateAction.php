<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\AcademicRequirement;
use Agentcis\PartnerDatabase\DegreeLevels;
use Agentcis\PartnerDatabase\Events\ProductDetailUpdated;
use Agentcis\PartnerDatabase\Model\Branch;
use Agentcis\PartnerDatabase\Model\Product;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductDetailUpdateAction
{
    use ValidatesRequests;

    public function __invoke($productId, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'branches' => 'required',
            'degree_level' => 'nullable',
            'academic_score_type' => 'required_with:academic_score|nullable|in:percentage,GPA',
            'academic_score' => [
                'bail',
                'required_with:academic_score_type',
                'nullable',
                'numeric',
            ],
        ]);
        /**
         * @var $product Product
         */
        $product = Product::query()->where('id', $productId)->withTrashed()->first();
        $product->fill($request->only([
            'name',
            'intake_month',
            'duration',
            'description',
            'category_id',
        ]));
        $product->forceFill([
            'academic_requirement' => AcademicRequirement::fromString($request->get('degree_level'),
                $request->get('academic_score_type'), $request->get('academic_score'))->toArray(),
            'fees' => $request->get('fees', []),
            'subject_area_and_level' => $request->get('subject_area_and_level'),
            'english_test_score' => $request->get('english_test_score'),
            'other_test_score' => $request->get('other_test_score'),
        ]);

        $product->save();
        $branches =  Branch::query()
                        ->where('partner_id', $product->getAttribute('partner_id'))
                        ->whereIn('id', $request->input('branches'))->get();

        $product->branches()->sync($branches);

        ProductDetailUpdated::dispatch($product);
        return new JsonResponse([
            'id' => $product->getKey()
        ]);
    }
}
