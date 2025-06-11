<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Events\BranchDetailUpdated;
use Agentcis\PartnerDatabase\Model\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BranchUpdateAction
{
    public function __invoke($branchId, Request $request)
    {
        /** @var Branch $branch */
        $branch = Branch::query()->withTrashed()->where('id', $branchId)->first();

        if (Branch::HEAD_OFFICE === $request->get('type') && $branch->getAttribute('type') == Branch::OTHER_OFFICE) {
            Branch::query()->where('partner_id', $branch->getAttribute('partner_id'))
                ->where('type', Branch::HEAD_OFFICE)
                ->update(['type' => Branch::OTHER_OFFICE]);
        }

        $branch->fill($request->only([
            'name',
            'email',
            'phone_number',
            'city',
            'state',
            'zip_code',
            'street',
            'type'
        ]));
        $branch->forceFill([
            'country' => $request->get('country_id'),
        ]);
        $branch->save();

        BranchDetailUpdated::dispatch($branch);

        return new JsonResponse([], Response::HTTP_ACCEPTED);
    }
}
