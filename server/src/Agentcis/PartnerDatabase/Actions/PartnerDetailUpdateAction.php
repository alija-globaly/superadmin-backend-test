<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Events\PartnerDetailUpdated;
use Agentcis\PartnerDatabase\Model\Partner;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class PartnerDetailUpdateAction
{
    use ValidatesRequests;

    public function __invoke($partnerId, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => Rule::unique('partners', 'email')->ignore($partnerId),
        ]);

        $partner = Partner::query()->withTrashed()->where('id', (int) $partnerId)->first();
        if ($request->hasFile('logo')) {
            $this->validate($request, [
                'logo' => 'image',
            ]);
            $logoName = Str::random(40).'.'. $request->logo->guessExtension();
            $request->logo->storePubliclyAs('import/partner/logo', $logoName);
            $this->deletePartnerLogo($partner);
            $partner->setAttribute(
                'logo',
                sprintf('https://%s.s3.ap-southeast-2.amazonaws.com/import/partner/logo/' . $logoName, config('filesystems.disks.s3.bucket'))
            );
        }elseif(!$request->filled('logo')) {
            $this->deletePartnerLogo($partner);
            $partner->setAttribute('logo', '');
        }

        $partner->fill($request->only([
            'name',
            'email',
            'phone_number',
            'fax',
            'website',
            'country',
            'street',
            'city',
            'state',
            'zip_code',
            'registration_number',
            'category_id',
        ]));
        $partner->save();

        PartnerDetailUpdated::dispatch($partner);

        return new JsonResponse([], Response::HTTP_CREATED);
    }

    private function deletePartnerLogo($partner)
    {
        Storage::disk('s3')->delete( str_replace('https://agentcisapp.s3.ap-southeast-2.amazonaws.com/', '', $partner->getAttribute('logo')));
    }
}
