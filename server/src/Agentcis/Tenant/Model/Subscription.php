<?php

namespace Agentcis\Tenant\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    const ON_TRIAL = 'OnTrial';
    const FREE = 'Free';
    const TRIAL_EXPIRED = 'TrialExpired';
    const NOT_RENEWING = 'NotRenewing';
    const GRACE_PERIOD = 'GracePeriod';
    const EXPIRED = 'Expired';
    const ARCHIVED = 'Archived';
    const ACTIVE = 'Active';

    protected $connection = 'agentcis';

    protected $appends = ['status'];
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'trial_ends_at',
        'ends_at',
        'created_at',
        'updated_at',
        'card_expires_at',
        'first_card_at',
    ];

    /**
     * Determine if the subscription is within its trial period.
     *
     * @return bool
     */
    public function onTrial()
    {
        if (is_null($this->getAttribute('trial_ends_at'))) {
            return false;
        }

        return Carbon::today()->lt($this->getAttribute('trial_ends_at'));
    }
    /**
     * Determine if the subscription is no longer active.
     *
     * @return bool
     */
    public function cancelled()
    {
        return !is_null($this->getAttribute('ends_at'));
    }
    /**
     * Check if trial expired status.
     *
     * @return bool
     */
    public function trialExpired()
    {
        if (is_null($this->getAttribute('trial_ends_at'))) {
            return false;
        }

        return Carbon::today()->gt($this->getAttribute('trial_ends_at'));
    }
    /**
     * Determine if the subscription is within its subscription period after cancellation.
     *
     * @return bool
     */
    public function onSubscriptionPeriod()
    {
        if (is_null($endsAt = $this->getAttribute('ends_at'))) {
            return false;
        }

        return Carbon::now()->lt(Carbon::instance($endsAt));
    }
    public function onGracePeriod()
    {
        $now = Carbon::now();

        return $this->cancelled()
            && !$this->onSubscriptionPeriod()
            && $now->lt($this->getAttribute('ends_at')->copy()->addDays(config('services.zoho.grace_period')));
    }

    public function getStatusAttribute()
    {
        return $this->getStatus();
    }
    /**
     * Gets subscription status.
     *
     *  no need to check if archived because there is no requirement of archiving now
     *  but we may need that later
     *
     * @return string
     */
    public function getStatus()
    {
        if ($this->onTrial() && !$this->cancelled()) {
            return self::ON_TRIAL;
        }

        if ($this->trialExpired()) {
            return self::TRIAL_EXPIRED;
        }

        if (!$this->cancelled()) {
            return self::ACTIVE;
        }

        if ($this->onSubscriptionPeriod()) {
            return self::NOT_RENEWING;
        }

        if ($this->onGracePeriod()) {
            return self::GRACE_PERIOD;
        }

        return self::EXPIRED;
    }
}
