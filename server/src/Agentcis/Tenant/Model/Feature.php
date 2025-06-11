<?php

namespace Agentcis\Tenant\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feature extends Model
{
    /**
     * Feature code names
     */
    const USERS_COUNT = 'users';
    const INVOICE_PER_MONTH = 'invoice_per_month';
    const WORKFLOW_COUNT = 'customizable_workflow';
    const STORAGE_LIMIT = 'data_storage';
    const TASK_LIMIT = 'task_limit';

    const DEFAULT_STORAGE_LIMIT = 50;

    /**
     * Master database connection.
     *
     * @var string
     */
    protected $connection = 'agentcis';

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'subscription_features';

    /**
     * Indicates if all mass assignment is enabled.
     *
     * @var bool
     */
    protected static $unguarded = true;

    /**
     * Feature belongs to many plans
     *
     * @return BelongsToMany
     */
    public function plans()
    {
        return $this->belongsToMany(
            Plan::class,
            'subscription_plan_features',
            'subscription_feature_id',
            'subscription_plan_id'
        )
            ->withPivot('limit');
    }

    /**
     * Check if feature has a limit
     *
     * @return bool
     */
    public function hasLimit()
    {
        return (null !== $this->pivot->limit) && ($this->pivot->limit > -1);
    }

    /**
     * Find the feature by its code name
     *
     * @param string $featureCodeName
     *
     * @return Feature
     */
    public function findByCodeName($featureCodeName)
    {
        return $this->where('code_name', $featureCodeName)->firstOrFail();
    }
}
