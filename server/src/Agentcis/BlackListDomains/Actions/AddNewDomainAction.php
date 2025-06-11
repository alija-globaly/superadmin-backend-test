<?php

namespace Agentcis\BlackListDomains\Actions;

use Agentcis\BlackListDomains\BlockedDomainRepository;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddNewDomainAction
{
    use ValidatesRequests;

    public function __invoke(Request $request, BlockedDomainRepository $blockedDomainRepository)
    {
        $this->validate($request, [
            'names' => 'required|array',
            'names.*' => 'regex:/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i'
        ]);

        // @todo validate provided domains
        $blockedDomainRepository->add($request->input('names'));

        return new JsonResponse();
    }
}
