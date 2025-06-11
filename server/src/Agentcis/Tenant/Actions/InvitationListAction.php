<?php

namespace Agentcis\Tenant\Actions;

use Agentcis\Tenant\Model\Invitation;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\QueryBuilder;

class InvitationListAction
{
    public function __invoke()
    {
        $invitations = QueryBuilder::for(Invitation::query()
            ->select(['id', 'token', 'email', 'created_at', 'tenant_id', 'updated_at', 'is_paid', 'plan_details'])
            ->latest())
            ->jsonPaginate();

        return new class($invitations) extends ResourceCollection
        {
            public function toArray($request)
            {
                $signUpDomain = config('services.registration.origin');


                return $this->collection->map(function ($resource) use ($signUpDomain) {
                    $planDetails = json_decode($resource->plan_details, true);

                    $invitationUrl = sprintf(
                        "%s/sign-up.html?email=%s&token=%s&plan=%s&payment_currency=%s&user_quantity=1&cycle=%s&ref=%s",
                        $signUpDomain,
                        $resource->email,
                        $resource->token,
                        $planDetails['plan'] ?? null,
                        $planDetails['currency'] ?? null,
                        $planDetails['billing_cycle'] ?? null,
                        $resource->referrer ?? null
                    );

                    return [
                        'id' => $resource->id,
                        'token' => $resource->token,
                        'email' => $resource->email,
                        'url' => $invitationUrl,
                        'tenant_id' => $resource->tenant_id,
                        'status' => $this->when(null === $resource->tenant_id, function ()  use ($resource) {
                            if ($resource->updated_at > now()->subMinutes(env('AGENTCIS_INVITATION_VALIDITY'))) {
                                return 'Pending';
                            } else {
                                return 'Expired';
                            }
                        }, 'Accepted'),
                        'is_paid' => $resource->is_paid,
                        'created_at' => Carbon::parse($resource->created_at)->toFormattedDateString(),
                        'updated_at' => Carbon::parse($resource->updated_at)->toFormattedDateString(),
                    ];
                });
            }
        };
    }
}
