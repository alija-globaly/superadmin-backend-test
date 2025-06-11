<?php

namespace Agentcis\Tenant\Model;

use Illuminate\Database\Eloquent\Model;

class TenantFeature extends Model
{
    protected $connection = 'agentcis';

    public $timestamps = false;
    protected $table = 'tenants_subscription_features';
    protected $fillable = [
        'code_name',
        'limit'
    ];
}
