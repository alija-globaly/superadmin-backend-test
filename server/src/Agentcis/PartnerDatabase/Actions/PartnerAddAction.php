<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Model\Branch;
use Agentcis\PartnerDatabase\Model\Partner;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PartnerAddAction
{
    use ValidatesRequests;

    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => Rule::unique('partners', 'email'),
            'logo' => ['sometimes', 'image']
        ]);
        $partner = new Partner;

        if ($request->hasFile('logo')) {
            $logoName = Str::random(40) . '.' . $request->logo->guessExtension();
            $request->logo->storePubliclyAs('import/partner/logo', $logoName);
            $partner->setAttribute(
                'logo',
                'https://agentcisapp.s3.ap-southeast-2.amazonaws.com/import/partner/logo/' . $logoName
            );
        }
        $partner->fill($request->only([
            'name',
            'email',
            'phone_number',
            'fax',
            'website',
            'country',
            'category_id',
            'street',
            'city',
            'state',
            'zip_code',
            'currency_code',
            'registration_number',
        ]));
        $partner->save();
        $partner->branches()->save(new Branch($request->only([
                'email',
                'phone_number',
                'street',
                'city',
                'state',
                'zip_code',
                'country',
            ]) + ['name' => 'Head Office', 'type' => 'Head Office']));

        return new JsonResponse([
            'id' => $partner->getKey()
        ]);
    }
}
