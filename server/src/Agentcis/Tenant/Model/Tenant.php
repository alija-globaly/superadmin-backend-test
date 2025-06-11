<?php

namespace Agentcis\Tenant\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $connection = 'agentcis';

    protected $table = 'tenants';

    public const MORPH_ALIAS = 'tenant';

    protected $fillable = [
        'first_name',
        'last_name',
        'business_name',
        'email',
        'phone_number',
        'country',
        'meta',
    ];

    protected $hidden = [
        'db_name',
        'db_username',
        'db_password',
        'customer_id',
        'subscription_id'
    ];

    /**
     * Tenant has many features
     *
     * @return HasMany
     */
    public function features()
    {
        return $this->hasMany(
            TenantFeature::class,
            'tenant_id'
        );
    }

    /**
     * @param $featureCode
     * @return mixed
     */
    public function findFeature($featureCode)
    {
        return $this->features()->where('code_name', $featureCode)->first();
    }

    /**
     * Get feature with limit of a tenant
     *
     * @param string $featureCode
     *
     * @return mixed
     */
    public function getFeatureWithLimit(string $featureCode)
    {
        $feature = $this->findFeature($featureCode);

        if ($feature) {
            return $feature->toArray();
        }

        $featureArray = collect((new Plan($this->plan_code))->getFeatures())->get($featureCode);

        return [
            'code_name' => $featureCode,
            'limit' => (is_array($featureArray) && array_key_exists('limit', $featureArray)) ? $featureArray['limit'] : null,
        ];
    }

    public function isSubdomainAvailable(string $subdomain): bool
    {
        return self::query()->where('subdomain', $subdomain)->count() === 0;
    }
}
