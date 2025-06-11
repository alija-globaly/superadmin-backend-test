<?php

namespace Agentcis\Dashboard\Actions;

use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class TenantRegistrationReportAction
{
    public function __invoke()
    {
        return [
            'data' => [
                'tenants_by_plan' => DB::connection('agentcis')
                    ->table('tenants')
                    ->join('subscription_plans as sp', 'tenants.plan_id', '=', 'sp.id')
                    ->selectRaw(new Expression(' CONCAT(sp.name, \' --\', sp.type) as plan_name, count(plan_id) total_tenants'))
                    ->groupBy('tenants.plan_id')
                    ->get(),
                'tenants_by_country' => DB::connection('agentcis')
                    ->table('tenants')
                    ->groupBy('country')
                    ->selectRaw(new Expression('tenants.country, count(tenants.id) as total_tenants'))
                    ->orderBy('total_tenants', 'DESC')
                    ->limit(15)
                    ->get()
            ],
        ];
    }
}
