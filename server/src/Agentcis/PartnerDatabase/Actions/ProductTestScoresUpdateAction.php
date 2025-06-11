<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\AcademicRequirement;
use Agentcis\PartnerDatabase\DegreeLevels;
use Agentcis\PartnerDatabase\Events\ProductTestScoreUpdated;
use Agentcis\PartnerDatabase\Model\Branch;
use Agentcis\PartnerDatabase\Model\Product;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductTestScoresUpdateAction
{
    use ValidatesRequests;

    public function __invoke($productId, Request $request)
    {
        /**
         * @var $product Product
         */
        $product = Product::query()->where('id', $productId)->first();
        $product->forceFill([
            'english_test_score' => json_encode($request->get('englishTestScores'), true),
            'other_test_score' => json_encode($request->get('otherTestScores')),
        ]);

        $product->save();

        ProductTestScoreUpdated::dispatch($product);

        return new JsonResponse([], Response::HTTP_ACCEPTED);
    }
}
