<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\AcademicRequirement;
use Agentcis\PartnerDatabase\DegreeLevels;
use Agentcis\PartnerDatabase\Events\ProductFeeUpdated;
use Agentcis\PartnerDatabase\Model\Branch;
use Agentcis\PartnerDatabase\Model\Product;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductFeeUpdateAction
{
    use ValidatesRequests;

    public function __invoke($productId, Request $request)
    {
        $this->validate($request, [
            'fees' => 'required',
        ]);
        /**
         * @var $product Product
         */
        $product = Product::query()->where('id', $productId)->first();
        $product->forceFill([
            'fees' => $request->get('fees')
        ]);

        $product->save();

        ProductFeeUpdated::dispatch($product);
        return new JsonResponse([], Response::HTTP_ACCEPTED);
    }
}
