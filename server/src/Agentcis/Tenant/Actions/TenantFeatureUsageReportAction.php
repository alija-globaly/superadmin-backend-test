<?php

namespace Agentcis\Tenant\Actions;

use Agentcis\Tenant\Model\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantFeatureUsageReportAction
{
    public function __invoke($tenantId)
    {
        $tenant = Tenant::query()->where('tenants.id', $tenantId)
            ->select(
                'tenants.id',
                'tenants.business_name',
                'tenants.first_name',
                'tenants.last_name',
                'tenants.email',
                'tenants.subdomain',
                'tenants.db_name',
                'tenants.phone_number',
                'tenants.country',
                'tenants.plan_code',
                'tenants.subscription_status',
                'tenants.payment_currency',
                'tenants.applicable_for_special_discount',
                'tenants.created_at',
                'tenants.updated_at')->first();

        $table = $tenant->db_name . '.%s';
        $tenantFeatureUsage = DB::table(sprintf($table, 'usages'))
            ->whereNull('valid_upto')
            ->select(['id', 'feature_name', 'value'])
            ->get();

        return [
            'data' => $featuresLimit = $tenant->features()->get()
                ->reject(function ($item) {
                    return null === $item->limit;
                })->map(function ($item) use ($table, $tenantFeatureUsage) {
                    $isStorage = $item->code_name === 'storage';
                    $isUser = $item->code_name === 'users';
                    $featureCode = Str::replaceFirst('email-', '', $item->code_name);
                    $usage = $tenantFeatureUsage->where('feature_name', $featureCode)->pluck('value')->first();
                    return [
                        'id' => $item->id,
                        'display_name' => ucfirst(Str::replaceFirst('-', ' ', $item->code_name)),
                        'code_name' => $item->code_name,
                        'limit' => $isStorage ? $item->limit . ' GB' : $item->limit,
                        'value' => $isStorage ? $this->formatBytes($usage) : (($isUser) ? DB::table(sprintf($table,
                            'users'))->where('status', 1)->count() : (int)$usage),
                    ];
                })->toArray()
        ];
    }

    public function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
