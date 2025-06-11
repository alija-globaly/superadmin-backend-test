<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Events\NewBranchAdded;
use Agentcis\PartnerDatabase\Model\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BranchAddAction
{
    public function __invoke(Request $request)
    {
        if (Branch::HEAD_OFFICE === $request->get('type')) {
            Branch::query()->where('partner_id', $request->get('partner_id'))
                ->where('type', Branch::HEAD_OFFICE)
                ->update(['type' => Branch::OTHER_OFFICE]);
        }

        $branch = new Branch;
        $branch->fill($request->only([
            'name',
            'email',
            'phone_number',
            'city',
            'state',
            'street',
            'type',
            'partner_id',
            'zip_code',
        ]));

        $branch->forceFill([
            'country' => $request->get('country_id'),
            'partner_id' => $request->get('partner_id')
        ]);
        $branch->save();

        NewBranchAdded::dispatch($branch);

        return new JsonResponse([
            'id' => $branch->getKey()
        ], Response::HTTP_ACCEPTED);
    }
}
