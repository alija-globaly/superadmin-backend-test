<?php

namespace Agentcis\Tenant\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plan extends Model
{
    protected $table = 'subscription_plans';

    protected $connection = 'agentcis';

    /**
     * Plan belongs to many feature.
     *
     * @return BelongsToMany
     */
    public function features()
    {
        return $this->belongsToMany(
            Feature::class,
            'subscription_plan_features',
            'subscription_plan_id',
            'subscription_feature_id'
        )
            ->withPivot('limit');
    }

}
