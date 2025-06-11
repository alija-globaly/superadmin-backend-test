<?php

namespace Agentcis\Tenant\Model;

class Status
{
    const IN_TRIAL = 1;
    const IN_TRIAL_LABEL = 'In Trial';

    const ACTIVE = 2;
    const ACTIVE_LABEL = 'Active';

    const NON_RENEWING = 3;
    const NON_RENEWING_LABEL = 'Non renewing';

    const CANCELLED = 4;
    const CANCELLED_LABEL = 'Cancelled';

    const STATUSES = [
        self::IN_TRIAL => self::IN_TRIAL_LABEL,
        self::ACTIVE => self::ACTIVE_LABEL,
        self::NON_RENEWING => self::NON_RENEWING_LABEL,
        self::CANCELLED => self::CANCELLED_LABEL
    ];

    const STATUSES_MAP = [
        'in_trial' => self::IN_TRIAL,
        'active' => self::ACTIVE,
        'non_renewing' => self::NON_RENEWING,
        'cancelled' => self::CANCELLED
    ];


    /**
     * @var int
     */
    private $status;

    public function __construct(int $status)
    {
        $this->status = $status;
    }

    public static function fromChargebeeStatus($status) {
        return new self(self::STATUSES_MAP[$status]);
    }

    public function identifier()
    {
        return $this->status;
    }
    public function label()
    {
        return self::STATUSES[$this->status];
    }
}
