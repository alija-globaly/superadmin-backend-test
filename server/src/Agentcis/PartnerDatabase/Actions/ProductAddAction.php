<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Events\NewProductDetailAdded;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Agentcis\PartnerDatabase\AcademicRequirement;
use Agentcis\PartnerDatabase\Model\Product;
use Agentcis\PartnerDatabase\DegreeLevels;
use Agentcis\PartnerDatabase\Model\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductAddAction
{
    use ValidatesRequests;

    /**
     * @param Request $request
     * @return array|JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'name' => 'required|max:255',
            'branches' => 'required',
            'academic_score_type' => 'nullable|required_with:academic_score|in:percentage,GPA',
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
        $product = new Product($request->only([
            'name',
            'intake_month',
            'duration',
            'description',
            'category_id',
        ]));
        $product->forceFill([
            'partner_id' => $request->get('partner_id'),
            'fees' => $request->get('fees', []),
            'subject_area_and_level' => $request->get('subject_area_and_level'),
            'english_test_score' => $request->get('english_test_score'),
            'other_test_score' => $request->get('other_test_score'),
            'academic_requirement' => AcademicRequirement::fromString($request->get('degree_level'),
                $request->get('academic_score_type'), $request->get('academic_score'))->toArray(),
        ]);

        Log::info('Product data before save:', $product->toArray());

        $product->save();
        $branches = Branch::query()
            ->where('partner_id', $product->getAttribute('partner_id'))
            ->whereIn('id', $request->input('branches'))->get();

        $product->branches()->sync($branches);

        NewProductDetailAdded::dispatch($product);


        return new JsonResponse([
            'id' => $product->getKey()
        ]);
    }
}
